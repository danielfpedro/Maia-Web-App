<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Network\Exception\NotFoundException;

class RelatoriosController extends AppController
{

    public function porChecklist()
    {
        $this->loadModel('Visitas');

        // $temFiltro = false;
        // if ($this->request->query('responsavel') || $this->request->query('checklist') || $this->request->query('usuario_vinculado')) {
        //     $temFiltro = true;
        // }

        $checklist = null;
        $auditor = null;
        $auditorSupervisionado = null;

        // Checklist é obrigatorio, por exemplo, ele não pode querer tdas as visitas
        // de um auitor semselcionar o checklist, pois este relatorio mostra tudo de uma checklist
        if ($this->request->query('checklist')) {
            $checklist = $this->Visitas->Checklists->todosDoMeuGrupo('all', $this->Auth->user())
                ->where([
                    'Checklists.id' => (int)$this->request->query('checklist'),
                ])
                ->first();
        }
        // Pego ele separado pq pode aconteceter de não ter nenhuma visita para mostrar
        // então eu pego o nome aqui
        if ($this->request->query('auditores')) {
            $auditor = $this->Visitas->Usuarios->todosDoMeuGrupo('all', $this->Auth->user())
                ->where(['Usuarios.id' => (int)$this->request->query('auditores')])
                ->first();
        }
        // Mesmo caso do acima
        if ($this->request->query('auditor_supervisionado')) {
            $auditorSupervisionado = $this->Visitas->Usuarios->todosDoMeuGrupo('all', $this->Auth->user())
                ->where(['Usuarios.id' => (int)$this->request->query('auditor_supervisionado')])
                ->first();
        }

        $visitas = [];


        if ($checklist) {

            $mapper = function ($visita, $key, $mapReduce) {
                $visita->setRespostasFlags();
                //debug($visita->checklist->perguntas[1]->resposta->alternativa_selecionada);
                $visita->ordenaSetores();
                $visita->setPerguntasNosSetores();
                //dd($visita->checklist->perguntas[1]->resposta->alternativa_selecionada);

                // if ($key == 6) {
                //     dd($mapReduce);
                //     dd($mapReduce->toArray()[0]->checklist->perguntas[1]->resposta->alternativa_selecionada);
                // }
                // debug($key);
                $mapReduce->emit($visita, $key);
            };

            $visitasPre = $this->Visitas->find()
                ->where([
                    'Visitas.checklist_id' => (int)$checklist->id,
                    'Visitas.dt_encerramento IS NOT' => null,
                    'Visitas.deletado' => false
                ])
                ->contain([
                    'Lojas',
                    'Usuarios',
                    // Preciso da alternativa selcionada para calcular o atingimento pois nela contem o valor
                    'Respostas' => function($query) {
                        $query
                            ->contain(['AlternativaSelecionada']);

                        if ($this->request->query('setores')) {
                            $query
                                ->matching('Perguntas', function($query) {
                                    return $query
                                        ->where([
                                            'Perguntas.setor_id IN' => $this->request->query('setores')
                                        ]);
                                });
                        }

                        if ($this->request->query('perguntas')) {
                            $query
                                ->matching('Perguntas', function($query) {
                                    return $query->where([
                                        'Perguntas.id IN' => $this->request->query('perguntas')
                                    ]);
                                });
                        }

                        return $query;
                    },
                    // Preciso das pergunta para calcular o maximo possivel nesta visita e alternativas tb
                    'Checklists' => function($query) {
                        return $query
                            ->contain([
                                'OrdemSetores.Setores' => function($query) {
                                    if ($this->request->query('setores')) {
                                        $query->where([
                                            'Setores.id IN' => $this->request->query('setores')
                                        ]);
                                    }
                                    return $query;
                                },
                                'Perguntas' => function($query) {
                                    $query
                                        ->contain(['Alternativas']);
                                    if ($this->request->query('perguntas')) {
                                        $query->where([
                                            'Perguntas.id IN' => $this->request->query('perguntas')
                                        ]);
                                    }

                                    if ($this->request->query('setores')) {
                                        $query->where([
                                            'Perguntas.setor_id IN' => $this->request->query('setores')
                                        ]);
                                    }

                                    return $query;

                                }
                            ]);
                    }
                ]);

                // Filtros
                if ($this->request->query('auditores')) {

                    $visitasPre->where([
                        'Visitas.usuario_id IN' => $this->request->query('auditores')
                    ])
                    ->contain(['Usuarios']);
                }
                if ($this->request->query('lojas')) {
                    $visitasPre->where([
                        'Visitas.loja_id IN' => $this->request->query('lojas')
                    ]);
                }
                // if ($this->request->query('auditor_supervisionado')) {
                //     $visitasPre->where([
                //         'Visitas.usuario_vinculado_id' => $this->request->query('auditor_supervisionado')
                //     ])
                //     ->contain(['UsuarioVinculado']);
                // }

                if ($this->request->query('prazo_de')) {
                    $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_de'));

                    $visitasPre->where([
                        'DATE(Visitas.dt_encerramento) >= ' => $prazoDe->format('Y-m-d')
                    ]);
                }
                if ($this->request->query('prazo_ate')) {
                    $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_ate'));

                    $visitasPre->where([
                        'DATE(Visitas.dt_encerramento) <= ' => $prazoDe->format('Y-m-d')
                    ]);
                }

            //$visitasPre = $visitasPre->all();
            $visitas = [];
            foreach ($visitasPre as $v) {
                $v->setRespostasFlags();
                $v->ordenaSetores();
                $v->setPerguntasNosSetores();
                $v->setAtingimentos();
                $visitas[] = $v->toArray();
            }

            if ($visitas) {
                $visitasCollection = new Collection($visitas);
                $visitas = $visitasCollection->sortBy('atingimento.atingido_porcentagem')->toArray();
            }
        }

        // Preencher filtros
        // Não precisa estar ativo pois eles podem querer ver resultado de nao ativos tb
        // faz todo sentido
        $checklistsComSetores = $this->Visitas->Checklists->todosVivosDoMeuGrupo('all', $this->Auth->user())
            ->find('dosMeusGruposDeAcessos', $this->Auth->user())
            ->select([
                'Checklists.id',
                'Checklists.nome',
                'Checklists.ativo'
            ])
            ->contain([
                'OrdemSetores.Setores' => function($query) {
                    return $query
                        ->select([
                            'Setores.id',
                            'Setores.nome'
                        ]);
                }
            ])
            ->order(['Checklists.ativo' => 'DESC', 'Checklists.nome']);
        $checklistsCombo = [];


        foreach ($checklistsComSetores as $checklistIteration) {
            $checklistsCombo[$checklistIteration->id] = $checklistIteration->nome . ((!$checklistIteration->ativo) ? ' (Inativo)' : '');
        }
        // dd($checklistsComSetores->toArray());

        $this->Visitas->Usuarios->displayField('short_name');
        $usuarios = $this->Visitas->Usuarios->todosVivosDoMeuGrupo('list', $this->Auth->user());
        $lojas = $this->Visitas->Lojas->todosVivosDoMeuGrupo('list', $this->Auth->user());
        // $setores = $this->Visitas->Checklists->OrdemSetores->Setores->todosVivosDoMeuGrupo('list', $this->Auth->user());

        $dadosDoGrafico = [];

        if ($visitas) {
            $visitasClone = $visitas;
            $dadosDoGrafico = [];
            $datasDasVisitas = [];

            foreach ($visitasClone as $visitaClone) {
                $datasDasVisitas[] = $visitaClone['dt_encerramento'];
            }

            // dd($datasDasVisitas);

            $visitasAgrupadasPorLoja = (new Collection($visitasClone))
                ->groupBy('loja_id')
                ->toArray();

            $totalLojas = count($visitasAgrupadasPorLoja);

            $visitasPorData = [];
            foreach ($visitasAgrupadasPorLoja as $lojaKey => $visitasDaLoja) {
                $visitasAgrupadasPorLoja[$lojaKey] = [];
                foreach ($visitasDaLoja as $i => $visita) {
                    $visitasAgrupadasPorLoja[$lojaKey][$i]['dt_encerramento'] = $visita['dt_encerramento'];
                    $visitasAgrupadasPorLoja[$lojaKey][$i]['dt_encerramento_string'] = ($totalLojas > 1) ? $visita['dt_encerramento']->format('d/m/y') : $visita['dt_encerramento']->format('d/m/y H:i:s');
                    $visitasAgrupadasPorLoja[$lojaKey][$i]['atingimento'] = $visita['atingimento']['atingido_porcentagem'];
                }
                $visitasAgrupadasPorLoja[$lojaKey] = (new Collection($visitasAgrupadasPorLoja[$lojaKey]))->groupBy('dt_encerramento_string')->toArray();
            }

            // debug($totalLojas);

            // dd($visitasAgrupadasPorLoja);

            // Se tem mais de uma loja eu preciso fazer a media se uma loja possuir mais de uma visita na mesma data
            if ($totalLojas > 1) {
                foreach ($visitasAgrupadasPorLoja as $lojaKey => $datas) {
                    // Se datas possuirem mais de uma visita e a gente tiver mais de uma loja eu faço a media por data
                    foreach ($datas as $keyDatas => $visitasDasDatas) {
                        $visitasAgrupadasPorLoja[$lojaKey][$keyDatas] = $visitasDasDatas[0];
                        if (count($visitasDasDatas) > 1) {
                            $soma = 0;
                            foreach ($visitasDasDatas as $i => $visita) {
                                $soma += $visita['atingimento'];
                            }
                            $visitasAgrupadasPorLoja[$lojaKey][$keyDatas]['atingimento'] = $soma / count($visitasDasDatas);
                        }
                    }
                }
            } else {
                // Quando é só uma loja eu não pego só a data e tb pego a hora e segundos então não pode ter mais de uma visita por data na loja
                // aqui eu faço o array da data virar valor... 
                // IMPORTANTE: Na teoria até podem ter duas visitas encerradas no mesmo horario e no mesmo segundo na mesma loja
                //  e no mesmo questionario mas vamos ignorar =]
                foreach ($visitasAgrupadasPorLoja as $lojaKey => $datas) {
                    // Se datas possuirem mais de uma visita e a gente tiver mais de uma loja eu faço a media por data
                    foreach ($datas as $keyDatas => $visitasDasDatas) {
                        $visitasAgrupadasPorLoja[$lojaKey][$keyDatas] = $visitasDasDatas[0];
                    }
                }
            }
            
            // dd($visitasAgrupadasPorLoja);

            usort($datasDasVisitas, function($a, $b) {
                $t1 = strtotime($a->format('Y-m-d H:i:s'));
                $t2 = strtotime($b->format('Y-m-d H:i:s'));
                return $t1 - $t2;
            });
            foreach ($datasDasVisitas as $i => $data) {
                $datasDasVisitas[$i] = ($totalLojas > 1) ? $data->format('d/m/y') : $data->format('d/m/y H:i:s');
            }

            if ($totalLojas > 1) {
                $datasDasVisitas = array_unique($datasDasVisitas);
            }

            // Loop nas datas e inserir cada visita da loja
            // IMPORTANTE: começar do 1 pois depois vou fazer a index 0
            $i = 0;
            $dadosDoGrafico = [];
            $header = [];
            foreach ($visitasClone as $visita) {
                $header[] = $visita['loja']['nome'];
            }
            $header = array_unique($header);
            // Insiro no começo
            array_unshift($header, 'Datas');
            $dadosDoGrafico[$i] = $header;
            $i = 1;
            //dd($visitasAgrupadasPorLoja);
            foreach ($datasDasVisitas as $keyData => $data) {
                $dadosDoGrafico[$i][] = $data;
                foreach ($visitasAgrupadasPorLoja as $keyLoja => $visitaDaLojaPorData) {
                    $atingimento = null;
                    if (isset($visitaDaLojaPorData[$data])) {
                        // debug($visitaDaLojaPorData[$data]['dt_encerramento']);
                        $atingimento = $visitaDaLojaPorData[$data]['atingimento'];
                    }
                    $dadosDoGrafico[$i][] = $atingimento;
                }
                $i++;
            }



        // IMPORTANTE!
        // Remover setores sem perguntas caso não tenha pergunta devido a algum filtro

            foreach ($visitas as $keyVisita => $visita) {
                foreach ($visita['checklist']['setores'] as $keySetor => $setor) {
                    if (!$visitas[$keyVisita]['checklist']['setores'][$keySetor]['perguntas']) {
                        unset($visitas[$keyVisita]['checklist']['setores'][$keySetor]);
                    }
                }
            }

        }


        $setoresSelecionados = [];
        if ($this->request->query('setores')) {
            $setoresSelecionados = $this->Visitas->Lojas->Setores->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
                ->select(['Setores.nome'])
                ->where(['Setores.id IN' => $this->request->query('setores')])
                ->extract('nome')
                ->toArray();
        }

        $this->set(compact(
            // Nome dos setores selecionados para mostrar quando for imprimir
            'setoresSelecionados',
            'dadosDoGrafico',
            'checklistsCombo',
            'checklistsComSetores',
            'checklist',
            'visitas',
            'usuarios',
            'auditor',
            'auditorSupervisionado',
            'lojas'
        ));
    }

    public function itensCriticos()
    {
        $this->loadModel('Visitas');
        $this->loadModel('ChecklistsPerguntasRespostas');

        $itensCriticosFinder = $this->ChecklistsPerguntasRespostas->find()
            ->contain([
                'Perguntas.Setores' => function($query) {
                    if ($this->request->query('setores')) {
                        $query->where([
                            'Setores.id IN' => $this->request->query('setores')
                        ]);
                    }
                    return $query;
                },
                'PlanosTaticos.Tarefas',
                'FotosRequeridas',
                'Visitas' => function($query) {
                    return $query
                        ->where([
                            'Visitas.grupo_id' => $this->Auth->user('grupo_id'),
                            [
                                'OR' => [
                                    'Visitas.dt_encerramento !=' => '',
                                    'Visitas.dt_encerramento IS NOT' => null
                                ]
                            ]
                        ])
                        ->contain([
                            'Lojas' => function($query) {
                                if ($this->request->query('lojas')) {
                                    $query->where([
                                        'Lojas.id IN' => $this->request->query('lojas') 
                                    ]);
                                }
                                return $query;
                            },
                            'Auditor' => function($query) {
                                if ($this->request->query('auditores')) {
                                    $query->where([
                                        'Auditor.id IN' => $this->request->query('auditores')
                                    ]);
                                }
                                return $query;
                            }
                        ]);
                },
                'AlternativasCriticas' => function($query) {
                    return $query;
                }
            ]);

        // Filtros
        if ($this->request->query('status_resolvido')) {
            switch ($this->request->query('status_resolvido')) {
                case 1:
                    $itensCriticosFinder->where([
                        'ChecklistsPerguntasRespostas.critico_resolvido IS' => null
                    ]);
                    break;
                case 2:
                    $itensCriticosFinder->where([
                        'ChecklistsPerguntasRespostas.critico_resolvido IS NOT' => null
                    ]);
                    break;
            }
        }

        if ($this->request->query('prazo_de')) {
            $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_de'));

            $itensCriticosFinder->where([
                'DATE(ChecklistsPerguntasRespostas.dt_resposta) >= ' => $prazoDe->format('Y-m-d')
            ]);
        }
        if ($this->request->query('prazo_ate')) {
            $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_ate'));

            $itensCriticosFinder->where([
                'DATE(ChecklistsPerguntasRespostas.dt_resposta) <= ' => $prazoDe->format('Y-m-d')
            ]);
        }

        $itensCriticos = $this->paginate($itensCriticosFinder);

        // Preencher filtros
        // Não precisa estar ativo pois eles podem querer ver resultado de nao ativos tb
        // faz todo sentido

        $usuarios = $this->Visitas->Usuarios->todosVivosDoMeuGrupo('list', $this->Auth->user());
        $lojas = $this->Visitas->Lojas->todosVivosDoMeuGrupo('list', $this->Auth->user());
        $setores = $this->Visitas->Checklists->OrdemSetores->Setores->todosVivosDoMeuGrupo('list', $this->Auth->user());

        $this->set(compact(
            'itensCriticos',
            'setores',
            'usuarios',
            'auditor',
            'lojas'
        ));
    }

}
