<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Routing\Router;
use Cake\Event\Event;

use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use WideImage\WideImage;

use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\BadRequestException;

use Cake\Collection\Collection;

use Cake\I18n\Time;

// Email
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;

/**
 * Visitas Controller
 *
 * @property \App\Model\Table\VisitasTable $Visitas
 */
class VisitasController extends AppController
{

    use MailerAwareTrait;

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['testarEmailCritico', 'teste']);
    }


    public function beforeFilter(Event $event)
    {
        if (in_array($this->request->action, ['historicoView', 'view', 'salvarRespostas', 'deletarSemAgendamento'])) {
            if (!$this->Visitas->exists(['id' => (int)$this->request->visitaId, 'usuario_id' => (int)$this->Auth->user('id')])) {
                throw new NotFoundException();
            }
        }

        parent::beforeFilter($event);

        $this->Security->config('unlockedActions', ['addSemAgendamento', 'salvarRespostas', 'fotosRequeridasUpload']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $visitas = $this
            ->Visitas
            ->find()
            ->select([
                'Visitas.id',
                'Visitas.teve_agendamento_flag',
                'Visitas.prazo'
            ])
            ->contain([
                'Checklists' => function($query) {
                    return $query
                        ->select([
                            'Checklists.id',
                            'Checklists.dt_modificado'
                        ]);
                },
                'Lojas' => function($query) {
                    return $query
                        ->select([
                            'Lojas.id',
                            'Lojas.nome',
                            'Lojas.lat',
                            'Lojas.lng',
                        ])
                        ->contain([
                            'Cidades' => function($query) {
                                return $query->select([
                                    'Cidades.id',
                                    'Cidades.nome',
                                    'Cidades.uf'
                                ]);
                            }
                        ]);
                }
            ])
            ->where([
                'Visitas.usuario_id' => (int)$this->Auth->user('id'),
                'Visitas.dt_encerramento IS' => null,
                'Visitas.deletado' => false,
                'Visitas.ativo' => true,
                'OR' => [
                    // Tudo no prazo ou sem agendamento
                    'Visitas.prazo >=' => Time::now()->format('Y-m-d'),
                    'Visitas.teve_agendamento_flag' => false
                ],
            ])
            ->order([
                'Visitas.prazo'
            ])
            ->limit(100);

        foreach ($visitas as $visita) {
            if ($visita->prazo) {
                $visita->prazo = $visita->prazo->format('Y-m-d');
            }
        }

        $this->set(compact('visitas'));
        $this->set('_serialize', ['visitas']);
    }


    public function historico()
    {
        // Mostrar Apenas encerradas(respondidas) e não vencidas sem ser respondidas
        $visitas = $this->Visitas
            ->find()
            ->select([
              'Visitas.id',
              'Visitas.prazo',
              'Visitas.dt_encerramento',
            ])
            ->contain([
                'Lojas' => function($q) {
                    return $q
                        ->select([
                            'Lojas.id',
                            'Lojas.nome',
                        ])
                        ->contain([
                            'Cidades' => function($q) {
                                return $q
                                    ->select([
                                        'Cidades.id',
                                        'Cidades.uf',
                                        'Cidades.nome'
                                    ]);
                            }
                        ]);
                }
            ])
            ->where([
                'Visitas.usuario_id' => $this->Auth->user('id'),
                'Visitas.ativo' => true,
                'OR' => [
                    'Visitas.dt_encerramento IS NOT' => null,
                    'Visitas.dt_encerramento !=' => '',
                ]
            ])
            ->order([
                'Visitas.dt_encerramento' => 'DESC'
            ])
            ->limit(100);
        // dd($visitas->toArray());
        $this->set(compact('visitas'));
        $this->set('_serialize', ['visitas']);
    }

    public function historicoView()
    {
        // No beforeFilter eu vejo se a visita pertence ao usuario logado entao aqui não preciso testar isso
        $visita = $this->Visitas->find()
            ->where([
                'Visitas.id' => $this->request->visitaId
            ])
            ->contain([
                'Respostas' => function($query) {
                    return $query
                        ->contain([
                            'AlternativaSelecionada',
                            'FotosRequeridas'
                        ]);
                },
                'Checklists' => function($query) {
                    return $query
                        ->contain([
                            'Perguntas' => function($query) {
                                return $query
                                    ->contain([
                                        'Alternativas',
                                        'Imagens'
                                    ])
                                    ->where([
                                        'Perguntas.deletado' => false
                                    ]);
                            },
                            'OrdemSetores.Setores'
                        ]);
                },
                'Lojas.Cidades'
            ])
            ->first();

        if (!$visita) {
            throw new NotFoundException();
        }

        // No app é feito o download das imagens então preciso do caminho completo
        $visita->checklist->fullbase = Router::fullBaseUrl() . $this->request->webroot;

        $visita->setRespostasFlags();
        $visita->ordenaSetores();
        $visita->setPerguntasNosSetores();
        $visita->setAtingimentos();

        // O app espera assim entao a gente faz o ele quer rs
        foreach ($visita->checklist->perguntas as $key => $pergunta) {

            if ($pergunta->resposta) {
                $pergunta->resposta_em_texto = $pergunta->resposta->resposta_em_texto;
                $pergunta->fotos_requeridas = $pergunta->resposta->fotos_requeridas;
                $pergunta->observacao_da_resposta = $pergunta->resposta->observacao;
                $pergunta->dt_resposta = ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('Y-m-d H:i:s') : null;
                // Deixa por ultimo
                $pergunta->resposta = $pergunta->alternativa_selecionada;
            } else {
                $pergunta->fotos_requeridas = [];
                $pergunta->resposta = [];
                $pergunta->observacao_da_resposta = null;
                $pergunta->resposta_em_texto = null;
                $pergunta->dt_resposta = null;
            }
        }

        $this->set(compact('visita'));
        $this->set('_serialize', ['visita']);
    }

    public function view()
    {
        // No beforeFilter eu vejo se a visita pertence ao usuario logado entao aqui não preciso testar isso
        $visita = $this->Visitas->find()
            ->where([
                'Visitas.id' => $this->request->visitaId,
                'Visitas.deletado' => false,
                'Visitas.ativo' => true,
            ])
            ->contain([
                'Respostas' => function($query) {
                    return $query
                        ->contain([
                            'AlternativaSelecionada',
                            'FotosRequeridas'
                        ]);
                },
                'Checklists' => function($query) {
                    return $query
                        ->contain([
                            'Perguntas' => function($query) {
                                return $query
                                    ->contain([
                                        'Alternativas' => function($query) {
                                            return $query
                                                ->order(['Alternativas.ordem']);
                                        },
                                        'Imagens'
                                    ])
                                    ->where([
                                        'Perguntas.deletado' => false
                                    ])
                                    ->order(['Perguntas.ordem']);
                            },
                            'OrdemSetores' => function($query) {
                                return $query
                                    ->contain([
                                        'Setores'
                                    ]);
                            }
                        ]);
                },
                'Lojas' => function($query) {
                    return $query
                        ->contain([
                            'Setores',
                            'Cidades'
                        ]);
                }
            ])
            ->first();

        if (!$visita) {
            throw new NotFoundException();
        }

        $visita->prazo = ($visita->prazo) ? $visita->prazo->format('Y-m-d') : null;

        // Retiro os setores que não tem na loja, faço direto no array e nao na query
        // pq sim
        // // Diz essa volta pq dando unset tava indo como objeto enfim
        $setoresDaLoja = (new Collection($visita->loja->setores))->extract('id')->toArray();

        $ordemSetoresSemFiltro = $visita->checklist->ordem_setores;
        $visita->checklist->ordem_setores = [];
        foreach ($ordemSetoresSemFiltro as $key => $ordemSetor) {
            if (in_array($ordemSetor->setor_id, $setoresDaLoja)) {
                $visita->checklist->ordem_setores[] = $ordemSetor;
            }
        }

        $perguntasSemFiltro = $visita->checklist->perguntas;
        $visita->checklist->perguntas = [];
        // Diz essa volta pq dando unset tava indo como objeto enfim
        foreach ($perguntasSemFiltro as $key => $pergunta) {
            if (in_array($pergunta->setor_id, $setoresDaLoja)) {
                $visita->checklist->perguntas[] = $pergunta;
            }
        }

        // No app é feito o download das imagens então preciso do caminho completo
        $visita->checklist->fullbase = Router::fullBaseUrl() . $this->request->webroot;

        $visita->setRespostasFlags();
        $visita->ordenaSetores();
        $visita->setPerguntasNosSetores();

        // dd($visita->checklist->setores);

        // O app espera assim entao a gente faz o ele quer rs
        foreach ($visita->checklist->perguntas as $key => $pergunta) {
            // dd($pergunta->resposta);
            if ($pergunta->resposta) {
                $pergunta->lat = $pergunta->resposta->lat;
                $pergunta->lng = $pergunta->resposta->lng;
                $pergunta->location_accuracy = $pergunta->resposta->location_accuracy;
                $pergunta->resposta_em_texto = $pergunta->resposta->resposta_em_texto;
                $pergunta->fotos_requeridas = $pergunta->resposta->fotos_requeridas;
                $pergunta->observacao_da_resposta = $pergunta->resposta->observacao;
                $pergunta->dt_resposta = ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('Y-m-d H:i:s') : null;
                // Deixa por ultimo
                $pergunta->resposta = $pergunta->resposta->alternativa_selecionada;
            } else {
                $pergunta->fotos_requeridas = [];
                $pergunta->resposta = [];
                $pergunta->observacao_da_resposta = null;
                $pergunta->resposta_em_texto = null;
                $pergunta->dt_resposta = null;
            }
        }

        $this->set(compact('visita'));
        $this->set('_serialize', ['visita']);
    }

    /**
     * Aqui eu salvo as respostas e coloco a data de encerramento mas seto a flag
     * travado
     */
    public function salvarRespostas()
    {
        //dd($this->request->getData());
        $visita = $this->Visitas->get($this->request->visitaId, ['contain' => ['Checklists', 'Respostas']]);

        $respostas = $this->request->getData('respostas');
        // $respostasGroupByPerguntas = $respostas->groupBy('checklists_pergunta_id');

        if ($visita->teve_agendamento_flag && $visita->vencida) {
            throw new BadRequestException("A visita está vencida.");
        }
        if ($visita->dt_encerramento) {
            throw new BadRequestException("A visita está encerrada.");
        }
        if (!$visita->ativo) {
            throw new BadRequestException("A visita está inativada.");
        }
        if ($visita->deletado) {
            throw new BadRequestException("A visita foi deletada.");
        }
        if ($visita->checklist->deletado) {
            throw new BadRequestException("A Checklist desta visita foi deletada pelo administrador.");
        }

        foreach ($respostas as $resposta) {
            // Vejo se a resposta já existe
            $respostaExistente = $this->Visitas->checklists->Perguntas->Respostas->find()
                ->where([
                    'Respostas.visita_id' => $visita->id,
                    'Respostas.checklists_pergunta_id' => $resposta['checklists_pergunta_id']
                ])
                ->first();

            // Cada pergunta só pode ter uma pergunta entao eu delete se ouver
            if ($respostaExistente) {
                $this->Visitas->checklists->Perguntas->Respostas->deleteOrFail($respostaExistente);
            }

            $respostaEntity = $this->Visitas->checklists->Perguntas->Respostas->newEntity(null, ['contain' => 'FotosRequeridas']);
            $resposta['visita_id'] = $visita->id;

            // debug($resposta);
            $respostaEntity = $this->Visitas->checklists->Perguntas->Respostas->patchEntity($respostaEntity, $resposta, ['associated' => ['FotosRequeridas']]);
            // dd($respostaEntity);
            $this->Visitas->checklists->Perguntas->Respostas->saveOrFail($respostaEntity);
        }

        if ((int)$this->request->query('tipo') == 2) {
            $dataToPatch['dt_encerramento'] = Time::now();
        }

        $dataToPatch['observacao'] = $this->request->getData('observacao');
        $this->Visitas->patchEntity($visita, $dataToPatch);

        $this->Visitas->saveOrFail($visita);

        // Se for encerramento mando os emails (critico e de encerramento)
        if ((int)$this->request->query('tipo') == 2) {
            $visita = $this->Visitas->find()
                ->where(['Visitas.id' => $visita->id])
                ->contain([
                    'GruposDeEmails',
                    'Respostas.AlternativaSelecionada',
                    'QuemGravou.Grupos',
                    'Usuarios',
                    'Lojas.Cidades',
                    'PlanosTaticosPreInfos',
                    'Respostas' => function($query) {
                        return $query
                            ->contain([
                                'AlternativaSelecionada',
                                'FotosRequeridas'
                            ]);
                    },
                    'Checklists.Perguntas' => function($query) {
                        return $query
                          ->contain([
                              'Alternativas',
                              'Setores'
                          ]);
                    }
                ])
                ->first();

            $visita->setRespostasFlags();

            // No find acima ele pega só as respostas criticas, ai abaixo
            // eu pego só as perguntas que tem resposta que como dito acima
            // são as criticas
            // Obs.: setRespostasFlags colocou as respostas nas perguntas, antes
            // elas estavam fora
            $perguntasComRespostaCritica = [];
            // dd($visita->checklist->perguntas);
            foreach ($visita->checklist->perguntas as $pergunta) {
                if ($pergunta->resposta && $pergunta->resposta->alternativa_selecionada && $pergunta->resposta->alternativa_selecionada->item_critico) {
                    $perguntasComRespostaCritica[] = $pergunta;
                }
            }

            // Teve ao menos uma resposta crítica
            if ($perguntasComRespostaCritica) {
                
                // Salvo os planos de ação se ele cadastrou o pre
                if ($visita->planos_taticos_pre_info) {
                    foreach ($perguntasComRespostaCritica as $perguntaComRespostaCritica) {

                        $whenEnd = Time::now()->addDays((int)$visita->planos_taticos_pre_info->prazo_dias);
                        $planoTaticoData = [
                            'solicitante_id' => $visita->planos_taticos_pre_info->solicitante_id,
                            'who_id' => $visita->planos_taticos_pre_info->who_id,
                            'when_start_placeholder' => Time::now()->format('d/m/Y'),
                            'when_start' => Time::now(),
                            'when_end_placeholder' => $whenEnd->format('d/m/Y'),
                            'when_end' => $whenEnd,
                            'what' => $perguntaComRespostaCritica->pergunta,
                            'why' => ($perguntaComRespostaCritica->resposta && $perguntaComRespostaCritica->resposta->alternativa_selecionada) ? $perguntaComRespostaCritica->resposta->alternativa_selecionada->alternativa . PHP_EOL . $perguntaComRespostaCritica->resposta->observacao : '-',
                            'checklists_perguntas_resposta_id' => $perguntaComRespostaCritica->resposta->id
                        ];

                        $planoTatico = $this->Visitas->Respostas->PlanosTaticos->newEntity($planoTaticoData);
                        $this->Visitas->Respostas->PlanosTaticos->save($planoTatico);
                        // DELETE O PRE PRA EVITAR ACUMULO DE DADOS
                        $this->Visitas->PlanosTaticosPreInfos->delete($visita->planos_taticos_pre_info);
                    }
                }

                $visita->setAtingimentos();
                // Pego os emails de destino
                $emailsCriticos = $visita->emails_criticos_extras_as_array;
                if ($visita->grupos_de_emails) {
                    foreach ($visita->grupos_de_emails as $grupo) {
                        $emailsCriticos = array_merge($emailsCriticos, $grupo->emails_criticos_as_array);
                    }
                }
                // Se tiver repetido mando só uma vez
                $emailsCriticos = array_unique($emailsCriticos);

                foreach ($emailsCriticos as $to) {
                    try {
                        $this
                            ->getMailer('Visitas')
                            ->send('encerramentoComRespostaCritica', [$to, $visita, $perguntasComRespostaCritica]);
                    } catch (\Exception $e) {
                        // Só da erro se for 500 que é de programação
                        // outros erros passa direto poe exemplo.. se
                        // um email invalido foi cadastrado não pode darpau aqui
                        // tem que passar direto
                        if ($e->getCode() == 500) {
                            throw $e;
                        }
                    }
                }
            }

            // Só manda se a visita está configurada para exibir dados publicos
            if ($visita->is_public == 1) {
                // Pego os emails
                $emailsResultado = $visita->emails_resultados_extras_as_array;
                if ($visita->grupos_de_emails) {
                    foreach ($visita->grupos_de_emails as $grupo) {
                        $emailsResultado = array_merge($emailsResultado, $grupo->emails_resultados_as_array);
                    }
                }
                // Se tiver repetido mando só uma vez
                $emailsResultado = array_unique($emailsResultado);

                foreach ($emailsResultado as $to) {
                    try {
                        $this
                            ->getMailer('Visitas')
                            ->send('encerramento', [$to, $visita]);
                    } catch (\Exception $e) {
                        // Só da erro se for 500 que é de programação
                        // outros erros passa direto poe exemplo.. se
                        // um email invalido foi cadastrado não pode darpau aqui
                        // tem que passar direto
                        if ($e->getCode() == 500) {
                            throw $e;
                        }
                    }
                }
            }


        }

        $response = $this->responseSuccess();

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function fotosRequeridasUpload()
    {

        $conn = ConnectionManager::get('default');
        $body = json_encode($this->request->getData());
        $conn->execute('INSERT INTO request_body_test (body) VALUES (\''.$body.'\')');

        $baseFolder = 'files' . DS . 'grupos' . DS . $this->Auth->user('grupo_id') . DS . 'visitas' . DS . 'fotos_requeridas' . DS . (int)$this->request->visitaId . DS;
        $folderDestino = new Folder(WWW_ROOT . $baseFolder , true, 0755);
        // dd($folderDestino->path);
        $file = $this->request->getData('file');

        $image = WideImage::load($file['tmp_name']);

        $w = $image->getWidth();
        $h = $image->getHeight();

        $maiorLado = ($w >= $h) ? $w : $h;

        if ($maiorLado > 1024) {
            $image = $image->resize(1024, 1024, 'inside');
        }

        $filename = md5((new \Datetime())->format('Y-m-d H:i:s') . $file['tmp_name']) . '.jpg';

        $image->saveToFile($folderDestino->path . $filename);

        $image->resize(300, 300, 'outside')->crop('center', 'center', 300, 300)->saveToFile($folderDestino->path . 'quadrada_' . $filename);

        // Para pegar o tamanho final
        $file = new File($folderDestino->path . $filename);

        $response = ['base_folder' => $baseFolder, 'filename' => $filename, 'filesize' => ($file->size() / 1024)];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function addSemAgendamento()
    {
        $data = $this->request->getData();

        $data['usuario_id'] = $this->Auth->user('id');
        $data['culpado_id'] = $this->Auth->user('id');
        $data['grupo_id'] = $this->Auth->user('grupo_id');
        $data['teve_agendamento_flag'] = false;
        $data['requerimento_localizacao'] = 1;

        $data['ativo'] = true;

        // Coloca os grupos de emails referentes a checklist e a loja
        $gruposDeEmails = $this->Visitas->GruposDeEmails->todosDoMeuGrupo('all', $this->Auth->user());
        $gruposDeEmails = $this->Visitas->GruposDeEmails->filtrarPorChecklist($gruposDeEmails, $data['checklist_id'])
            ->select(['GruposDeEmails.id']);

        $data['grupos_de_emails']['_ids'] = $gruposDeEmails->extract('id')->toArray();

        // Ignora validação do prazo pois aqui não tem prazo
        $visita = $this->Visitas->newEntity($data, ['validate' => 'OnlyApi']);

        $visita->validar_prazo = false;

        $this->Visitas->saveOrFail($visita);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function deletarSemAgendamento()
    {

        $visita = $this->Visitas->get($this->request->visitaId);

        if ($visita->teve_agendamento_flag) {
            throw new BadRequestException("Só visitas sem agendamento podem ser deletadas pelo app.");
        }
        if ($visita->culpado_novo_id != $this->Auth->user('id')) {
            throw new NotFoundException();
        }

        $visita->deletado = true;
        $this->Visitas->saveOrFail($visita);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function testarEmailCritico()
    {
        $visita = $this->Visitas->find()
            ->where(['Visitas.id' => 380])
            ->contain([
                'Respostas.AlternativaSelecionada',
                'QuemGravou.Grupos',
                'Usuarios',
                'Lojas.Cidades',
                'Respostas' => function($query) {
                    return $query
                        ->contain([
                            'AlternativaSelecionada',
                            'FotosRequeridas'
                        ]);
                },
                'Checklists.Perguntas' => function($query) {
                    return $query
                      ->contain([
                          'Alternativas',
                          'Setores'
                      ]);
                }
            ])
            ->first();

        $visita->setRespostasFlags();
        $visita->setAtingimentos();

        //dd($visita['atingimento']);

        // No find acima ele pega só as respostas criticas, ai abaixo
        // eu pego só as perguntas que tem resposta que como dito acima
        // são as criticas
        // Obs.: setRespostasFlags colocou as respostas nas perguntas, antes
        // elas estavam fora
        $perguntasComRespostaCritica = [];
        foreach ($visita->checklist->perguntas as $pergunta) {
            if ($pergunta->resposta) {
                $perguntasComRespostaCritica[] = $pergunta;
            }
        }

        // Se tiverem emails criticos configurados eu mando para todos
        foreach ($visita->loja->emails_criticos_as_array as $to) {
            try {
                $this
                    ->getMailer('Visitas')
                    ->send('encerramentoComRespostaCritica', [$to, $visita, $perguntasComRespostaCritica]);
            } catch (\Exception $e) {
                // Só da erro se for 500 que é de programação
                // outros erros passa direto poe exemplo.. se
                // um email invalido foi cadastrado não pode darpau aqui
                // tem que passar direto
                if ($e->getCode() == 500) {
                    throw $e;
                }
            }
        }

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');

    }

    public function teste()
    {
        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

}
