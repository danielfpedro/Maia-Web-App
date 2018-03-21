<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;

use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\BadRequestException;
use Cake\I18n\Time;

use Cake\Collection\Collection;

use Cake\Mailer\MailerAwareTrait;

use Cake\Auth\DefaultPasswordHasher;

/**
 * Visitas Controller
 *
 * @property \App\Model\Table\VisitasTable $Visitas
 */
class VisitasController extends AppController
{

    // Traits
    use MailerAwareTrait;

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        if (in_array($this->request->action, ['edit', 'delete', 'editNotificacoesPorEmail', 'editPlanosTaticosPreInfos'])) {
            if (!$this->Visitas->exists([
                'id' => (int)$this->request->visitaId,
                'grupo_id' => (int)$this->Auth->user('grupo_id'),
                'deletado' => false
            ])) {
                throw new NotFoundException();
            }
        }

        /**
         * Alguns campos são criados fora do helper do cake então tem que desabilitar a segurando de integridade de campos
         */
        $this->Security->config('unlockedActions', ['add', 'delete']);
        
    }

    // public function testeEnvioEmailCritico() {
    //     $visita = $this->Visitas->find()
    //         ->where(['Visitas.id' => 217])
    //         ->contain([
    //             'Respostas.AlternativaSelecionada',
    //             'QuemGravou.Grupos',
    //             'Usuarios',
    //             'Lojas.Cidades',
    //             'Respostas.AlternativasCriticas',
    //             'Checklists.Perguntas' => function($query) {
    //                 return $query
    //                   ->contain([
    //                       'Alternativas',
    //                       'Setores'
    //                   ]);
    //             }
    //         ])
    //         ->first();
    //
    //     $visita->setRespostasFlags();
    //
    //     // No find acima ele pega só as respostas criticas, ai abaixo
    //     // eu pego só as perguntas que tem resposta que como dito acima
    //     // são as criticas
    //     // Obs.: setRespostasFlags colocou as respostas nas perguntas, antes
    //     // elas estavam fora
    //     $perguntasComRespostaCritica = [];
    //     foreach ($visita->checklist->perguntas as $pergunta) {
    //         if ($pergunta->resposta) {
    //             $perguntasComRespostaCritica[] = $pergunta;
    //         }
    //     }
    //
    //     if ($perguntasComRespostaCritica) {
    //         // Se tiverem emails criticos configurados eu mando para todos
    //         foreach ($visita->loja->emails_criticos_as_array as $to) {
    //             try {
    //                 $this
    //                     ->getMailer('Visitas')
    //                     ->send('encerramentoComRespostaCritica', [$to, $visita, $perguntasComRespostaCritica]);
    //             } catch (\Exception $e) {
    //                 // Só da erro se for 500 que é de programação
    //                 // outros erros passa direto poe exemplo.. se
    //                 // um email invalido foi cadastrado não pode darpau aqui
    //                 // tem que passar direto
    //                 if ($e->getCode() == 500) {
    //                     throw $e;
    //                 }
    //             }
    //         }
    //     }
    // }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('Visitas.index', ['controller' => 'Visitas', 'action' => 'index']);

        $finder = $this->Visitas
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos');

        $finder
            ->select([
                'Visitas.id',
                'Visitas.cod',
                'Visitas.prazo',
                'Visitas.dt_encerramento',
                'Visitas.criado_em',
                'Visitas.requerimento_localizacao',
                'Visitas.token_visualizar_publico',
                'Visitas.teve_agendamento_flag',
                'Visitas.ativo',
                'Checklists.id',
                'Checklists.nome',
                'total_planos_taticos' => $finder->func()->count('PlanosTaticos.id')
            ])
            // Sem agendamento somente se tiver encerrado
            ->where([
                'OR' => [
                    'Visitas.teve_agendamento_flag' => true,
                    'Visitas.dt_encerramento IS NOT' => null
                ]
            ])
            ->contain([
                'UsuarioVinculado' => function($query) {
                    return $query
                        ->select([
                            'UsuarioVinculado.id',
                            'UsuarioVinculado.nome',
                        ]);
                },
                'Respostas' => function($query) {
                    return $query
                        ->select(['Respostas.id', 'Respostas.visita_id', 'Respostas.critico_resolvido'])
                        ->contain([
                            'AlternativasCriticas' => function($query) {
                                return $query
                                    ->select(['AlternativasCriticas.id']);
                            }
                        ]);
                },
                'QuemGravou' => function($query) {
                    return $query
                        ->select([
                            'QuemGravou.id',
                            'QuemGravou.nome',
                        ]);
                },
                'Usuarios' => function($query) {
                    return $query
                        ->select([
                            'Usuarios.id',
                            'Usuarios.nome',
                        ]);
                },
                'Lojas' => function($query) {
                    return $query
                        ->select([
                            'Lojas.id',
                            'Lojas.nome',
                        ]);
                },
            ])
            ->matching('Checklists', function($query) {
                return $query->find('dosMeusGruposDeAcessos', $this->Auth->user());
            })
            ->leftJoinWith('Respostas.PlanosTaticos', function($query) {
                return $query;
            })
            ->order(['Visitas.dt_encerramento' => 'DESC', 'Visitas.prazo' => 'ASC']);


        $subquery = $this->Visitas->Respostas->find();
        $subqueryRespostasCriticas = $subquery
            ->select([
                'Respostas.id',
            ])
            ->where(function ($exp, $q) {
                return $exp->equalFields('Visitas.id', 'Respostas.visita_id');
            });

        switch ($this->request->query('resposta_critica')) {
            // Somente com resposta critica
            case 1:
                $subqueryRespostasCriticas->contain(['AlternativasCriticas']);
                break;
            case 2:
                $subqueryRespostasCriticas
                    ->contain(['AlternativasCriticas'])
                    ->andWhere(['Respostas.critico_resolvido' => 0]);
                break;
        }
        if ($this->request->query('resposta_critica') > 0) {
            // Independente do filtro isso aqui sempre vai existir pois o where
            // para filtrar os resultados está na subquery acima
            $finder
                ->where(function ($exp, $q) use ($subqueryRespostasCriticas) {
                    return $exp->exists($subqueryRespostasCriticas);
                });
        }

        //dd($finder->toArray());

        /**
         * Filtro pelo responsável
         */
        if ((int)$this->request->query('auditor')) {
            $finder->where(['Visitas.usuario_id' => (int)$this->request->query('auditor')]);
        }
        /**
         * Filtro pela loja
         */
        if ((int)$this->request->query('loja')) {
            $finder->where(['Visitas.loja_id' => (int)$this->request->query('loja')]);
        }
         /**
          * Filtro pela checklist
          */
        if ((int)$this->request->query('questionario')) {
            $finder->where(['Visitas.checklist_id' => (int)$this->request->query('questionario')]);
        }
        /**
         * Filtro Prazo
         */
        if ((int)$this->request->query('prazo')) {
            $now = Time::now();
            switch ((int)$this->request->query('prazo')) {
                case 1:
                    $finder->where([
                        'Visitas.prazo <' => $now->format('Y-m-d')
                    ]);
                    break;
                case 2:
                    $finder->where([
                        'Visitas.prazo >=' => $now->format('Y-m-d')
                    ]);
                    break;
            }
            // $finder->where(['Visitas.dt_encerramento IS NOT' => null]);
        }
        /**
         * Filtro do status
         */
         if ((int)$this->request->query('encerramento')) {
             if ((int)$this->request->query('encerramento') == 1) {
                 $finder->where(['OR' => ['Visitas.dt_encerramento IS' => null, 'Visitas.dt_encerramento' => '']]);
             } else {
                 $finder->where(['OR' => ['Visitas.dt_encerramento IS NOT' => null, 'Visitas.dt_encerramento !=' => '']]);
             }
         }

         /**
          * Filtro prazo de
          */
        if ($this->request->query('prazo_de')) {
            $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_de'));

            $finder->where([
                'Visitas.prazo >= ' => $prazoDe->format('Y-m-d')
            ]);
        }

        /**
         * Filtro prazo ate
         */
       if ($this->request->query('prazo_ate')) {

           $prazoAte = Time::createFromFormat('d/m/Y', $this->request->query('prazo_ate'));

           $finder->where([
               'Visitas.prazo <= ' => $prazoAte->format('Y-m-d')
           ]);
       }

       /**
        * Filtro dt encerramento de
        */
      if ((int)$this->request->query('encerramento_de')) {
          $dtEncerramentoDe = Time::createFromFormat('d/m/Y', $this->request->query('encerramento_de'));

          $finder->where([
              'Visitas.dt_encerramento IS NOT' => null,
              'DATE(Visitas.dt_encerramento) >= ' => $dtEncerramentoDe->format('Y-m-d')
          ]);
      }

      /**
       * Filtro dt encerramento ate
       */
     if ((int)$this->request->query('encerramento_ate')) {

         $dtEncerramentoAte = Time::createFromFormat('d/m/Y', $this->request->query('encerramento_ate'));

         $finder->where([
             'Visitas.dt_encerramento IS NOT' => null,
             'DATE(Visitas.dt_encerramento) <= ' => $dtEncerramentoAte->format('Y-m-d')
         ]);
     }

        $finder->group(['Visitas.id']);

        $visitas = $this->paginate($finder);

        /**
        * Preencher combobox dos filtros
        */
        $usuarios = $this->Visitas->Usuarios
            ->todosAtivosDoMeuGrupo('list', $this->Auth->user())
            ->order(['Usuarios.nome']);

        $checklists = $this->Visitas->Checklists
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('dosMeusGruposDeAcessos', $this->Auth->user())
            ->find('list')
            ->order(['Checklists.nome']);
            
        $lojas = $this->Visitas->Lojas
            ->todosVivosDoMeuGrupo('list', $this->Auth->user())
            ->order(['Lojas.nome']);

        $this->set(compact('visitas', 'usuarios', 'checklists', 'lojas'));
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Visitas.index', ['action' => 'index']);

        // Não pode deletar visita que já encerrou, bem obvio né?
        $this->loadModel('Logs');

        $lojas = $this->Usuarios->Lojas
            ->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
            ->order(['Lojas.nome' => 'ASC'])
            ->contain(['Setores']);

        foreach ($lojas as $loja) {
            $visitas[] = $this->Visitas->newEntity();
        }
        $visitas['modelo'] = $this->Visitas->newEntity();

        $lojasError = false;

        if ($this->request->is('post')) {
            // dd($this->request->getData());
            $data = $this->request->getData();
            $modelo = $this->request->getData('modelo');

            $lojasSelecionadas = 0;

            foreach ($data as $key => $value) {
                
                $lojaSetada = (isset($data[$key]['loja_id'])) ? true : false;
                if ($lojaSetada) {
                    $lojasSelecionadas++;
                }
                if ($lojaSetada || $key == 'modelo') {
                    $data[$key]['usuario_id'] = $modelo['usuario_id'];
                    $data[$key]['checklist_id'] = $modelo['checklist_id'];
                    // $data[$key]['prazo_placeholder'] = $modelo['prazo_placeholder'];
                    $data[$key]['prazo'] = (isset($value['prazo_placeholder']) && $value['prazo_placeholder']) ? Time::createFromFormat('d/m/Y', $value['prazo_placeholder']) : null;
                    $data[$key]['grupo_id'] = $this->Auth->user('grupo_id');

                    // Se não marcou a checkbox plano atomatico não coloco
                    if ($key != 'modelo' && !$data[$key]['planos_taticos_pre_info']['flag']) {
                        unset($data[$key]['planos_taticos_pre_info']);
                    }
                    
                    // debug($data[$key]);
                    $visitas[$key] = $this->Visitas->newEntity($data[$key]);
                    // debug($visitas[$key]);
                } else {
                    $visitas[$key] = $this->Visitas->newEntity();
                }
            }

            // dd($visitas);

            // Se tiver só um (modelo) falo que deve selecionar ao menos um loja;
            if ($lojasSelecionadas < 1) {
                $lojasError = true;
            } else {
                // Nenhum pode ter erro
                $anyError = false;

                if (!$modelo['checklist_id']) {
                    $anyError = true;
                } else {
                    // Pego os setores da Checklist.. pego fora do foreach pois é
                    // o mesmo pra todos
                    $checklistComSetores = $this->Visitas->Checklists->get($modelo['checklist_id'], ['contain' => 'OrdemSetores']);
                    // dd($checklistComSetores);
                    $setoresDaChecklistIds = [];
                    foreach ($checklistComSetores['ordem_setores'] as $key => $setor) {
                        $setoresDaChecklistIds[] = $setor['setor_id'];
                    }

                    foreach ($visitas as $key => $visita) {
                        if ($visita->errors() && $key != 'modelo') {
                            $anyError = true;
                            break;
                        }
                        // Validando se a loja da visita tem ao menos um setor da checklist
                        if ($key != 'modelo' && $visita->loja_id) {


                            $lojaComSetores = $this->Visitas->Lojas->get($visita->loja_id, ['contain' => ['Setores']]);
                            //debug($lojaComSetores['setores']);
                            $aoMenosUm = false;

                            foreach ($lojaComSetores['setores'] as $setor) {
                                // dd($setor->id);
                                if (in_array($setor->id, $setoresDaChecklistIds)) {
                                    $aoMenosUm = true;
                                    break;
                                }
                            }

                            if (!$aoMenosUm) {
                                $visita->errors('geral', 'A loja da Visita deve conter ao menos um setor que a Checklist possui.');
                                $anyError = true;
                            }
                        }
                    }
                }
                                    //dd('aqui');
                if (!$anyError) {
                    foreach ($visitas as $key => $visita) {
                        // Devo checar o id pq a entidade vazia não tem erro de validação
                        // mas tb não tem conteudo ai salva ela vazia, desse jeito a gente
                        // só salva as que foram preenchidas
                        if ($key != 'modelo' && $visita->loja_id) {

                            $visita->teve_agendamento_flag = true;
                            $visita->is_public = 1;
                            $visita->validar_prazo = true;

                            if ($visita->planos_taticos_pre_info) {
                                $visita->planos_taticos_pre_info->set('grupo_id', $visita->grupo_id);
                            }

                            $this->Visitas->saveOrFail($visita);

                            //////////////////
                            // SALVANDO LOG //
                            //////////////////
                            $this->loadModel('Logs');

                            $visitaParaLog = $this->Visitas->get($visita->id, ['contain' => ['QuemGravou', 'Checklists', 'Lojas', 'Usuarios']]);

                            $dataLog = [
                                'modulo_id' => 1,
                                'logs_tipo_id' => 3,
                                'table_name' => 'visitas',
                                'ref' => $visita->id,
                                'autor_id' => $this->Auth->user('id'),
                                'grupo_id' => $this->Auth->user('grupo_id'),
                            ];
                            $dataLog = $this->Logs->patchData($dataLog, $visitaParaLog);
                            $log = $this->Logs->newEntity($dataLog);
                            $this->Logs->saveOrFail($log);            
                            //////////////////
                            // FIM SALVANDO LOG //
                            //////////////////

                            // dd($visita);
                            if ($visita->ativo) {
                                // Se salvou e está ativo eu mando email
                                // Pego os dados do usuario e do usuario vinculado
                                $visita = $this->Visitas->get($visita->id, ['contain' => ['Usuarios', 'Lojas.Cidades']]);
                                $quemCriou = $this->Visitas->Usuarios->get($this->Auth->user('id'), ['contain' => 'Grupos']);

                                // Coloco o try pq não quero jogar erro se der erro
                                // no envio do email, ou seja, se der erro no email passo direto
                                //
                                try {
                                    $this->getMailer('Visitas')->send('nova', [$visita, $quemCriou]);
                                } catch (\Exception $e) {
                                }


                            }
                        }
                    }

                    $this->Flash->set('A(s) visita(s) foi(ram) criada(s).', ['element' => 'Painel/success']);
                    return $this->redirect($this->breadcrumbRedirect('Visitas.index', ['action' => 'index']));
                }
            }
        }
        // Para selecionar as checkboxes
        $usuariosComLojas = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'auditor']]])
            ->select(['Usuarios.id'])
            ->contain(['Lojas' => function($query) {
                return $query
                    ->select(['Lojas.id']);
            }]);

        // Pego com os setores pq preciso para avisar caso uma loja não tenha todos
        $checklistsComSetores = $this->Visitas->Checklists->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
            ->select(['Checklists.id', 'Checklists.nome'])
            ->contain('OrdemSetores.Setores')
            ->order(['Checklists.nome']);
        // Aqui monto o array só de id e nome para popular a combobox
        $checklists = [];
        foreach ($checklistsComSetores as $checklist) {
            $checklists[$checklist->id] = $checklist->nome;
        }

        // USUARIOS
        $auditores = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'auditor']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $executantes = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'executante plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $solicitantes = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'controle plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);
        // USUARIOS FIM

        $setores = $this->Visitas->Lojas->Setores
            ->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
            ->order(['Setores.nome']);

        $requerimentoLocalizacaoOptions = $this->Visitas->requerimentoLocalizacaoOptions;

        $gruposDeEmails = $this->Visitas->Checklists->GruposDeEmails->todosDoMeuGrupo('list', $this->Auth->user())
            ->select(['nome']);

        $this->set(compact(
            'auditores',
            'executantes',
            'solicitantes',
            'checklists',
            'checklistsComSetores',
            'gruposDeEmails',
            'lojas',
            'lojasError',
            'requerimentoLocalizacaoOptions',
            'setores',
            'usuariosComLojas',
            'visitas',
            'breadcrumb'
        ));
    }

    /**
     * Edit method
     *
     * @param string|null $id Visita id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    // public function editPrazo()
    // {
    //     $visita = $this->Visitas->get($this->request->visitaId, [
    //         'fields' => [
    //             'Visitas.id',
    //             'Visitas.prazo',
    //             'Visitas.dt_encerramento',
    //         ],
    //         'contain' => [
    //             'Usuarios' => function($query) {
    //                 return $query->select(['Usuarios.id', 'Usuarios.nome']);
    //             },
    //             'Lojas' => function($query) {
    //                 return $query->select(['Lojas.id', 'Lojas.nome']);
    //             },
    //             'Checklists' => function($query) {
    //                 return $query->select(['Checklists.id', 'Checklists.nome']);
    //             },
    //         ]
    //     ]);
    //
    //     if ($visita->dt_encerramento) {
    //         throw new ForbiddenException("O Prazo da visita não pode ser editado pois ela já foi encerrada");
    //     }
    //
    //     if ($this->request->is(['patch', 'post', 'put'])) {
    //
    //         $visita = $this->Visitas->patchEntity($visita, ['prazo' => $this->_setPrazo()]);
    //
    //         if ($this->Visitas->save($visita)) {
    //             $this->Flash->set('O Prazo da visita foi alterado.', ['element' => 'Painel/success']);
    //
    //             return $this->redirect(['action' => 'index']);
    //         } else {
    //             $this->Flash->set('O Prazo da visita não foi alterado.', ['element' => 'Painel/error']);
    //         }
    //     }
    //
    //     $this->set(compact('visita'));
    // }

    public function editUsuarioVinculado()
    {
        // Pego esse monte de coisa pra exibir algumas informações na tela
        $visita = $this->Visitas->get($this->request->visitaId, [
            'fields' => [
                'Visitas.id',
                'Visitas.usuario_vinculado_id',
                'Visitas.prazo',
                'Visitas.ativo',
                'Visitas.dt_encerramento',
                'Visitas.requerimento_localizacao'
            ],
            'contain' => [
                'Usuarios' => function($query) {
                    return $query->select(['Usuarios.id', 'Usuarios.nome']);
                },
                'Lojas' => function($query) {
                    return $query->select(['Lojas.id', 'Lojas.nome']);
                },
                'Checklists' => function($query) {
                    return $query->select(['Checklists.id', 'Checklists.nome']);
                },
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $visita = $this->Visitas->patchEntity($visita, [
                'usuario_vinculado_id' => $this->request->getData('usuario_vinculado_id'),
                // preciso colocar isso para validar o usuario vinculado;
                'grupo_id' => $this->Auth->user('grupo_id')
            ]);

            if ($this->Visitas->save($visita)) {

                $this->Flash->set('O Usuário vinculado foi alterado.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set('A visita não foi alterada.', ['element' => 'Painel/error']);
            }
        }

        $usuarios = $this->Visitas->Usuarios->todosAtivosDoMeuGrupo('list', $this->Auth->user());

        $this->set(compact('visita', 'usuarios'));
    }

    public function editNotificacoesPorEmail()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Visitas.index', ['action' => 'index']);

        $visita = $this->Visitas->get($this->request->visitaId, [
            'contain' => [
                'Usuarios',
                'Lojas',
                'GruposDeEmails'
            ]
        ]);

        if ($this->request->is(['post', 'patch', 'put'])) {

            $visita = $this->Visitas->patchEntity($visita, $this->request->getData());

            if ($this->Visitas->save($visita)) {

                $this->Flash->set('As notificações por email foram alteradas.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('Visitas.index', ['action' => 'index']));
            } else {

                $this->Flash->set('As notificações por email não foram alteradas.', ['element' => 'Painel/error']);

            }
        }

        $gruposDeEmails = $this->Visitas->GruposDeEmails->todosDoMeuGrupo('list', $this->Auth->user());

        $this->set(compact('visita', 'gruposDeEmails', 'breadcrumb'));   
    }

    public function edit()
    {
        
        $breadcrumb['index'] = $this->breadcrumbRedirect('Visitas.index', ['action' => 'index']);

        // Pego esse monte de coisa pra exibir algumas informações na tela
        $visita = $this->Visitas->get($this->request->visitaId, [
            'fields' => [
                'Visitas.id',
                'Visitas.usuario_vinculado_id',
                'Visitas.prazo',
                'Visitas.ativo',
                'Visitas.dt_encerramento',
                'Visitas.requerimento_localizacao'
            ],
            'contain' => [
                'Usuarios' => function($query) {
                    return $query->select(['Usuarios.id', 'Usuarios.nome']);
                },
                'Lojas' => function($query) {
                    return $query->select(['Lojas.id', 'Lojas.nome']);
                },
                'Checklists' => function($query) {
                    return $query->select(['Checklists.id', 'Checklists.nome']);
                },
            ]
        ]);

        if ($visita->dt_encerramento) {
            throw new ForbiddenException("O Prazo da visita não pode ser editado pois ela já foi encerrada");
        }

        if ($this->request->is(['patch', 'post', 'put'])) {

            $visita = $this->Visitas->patchEntity($visita, [
                'grupo_id' => $this->Auth->user('grupo_id'),
                'validar_prazo' => true,
                'requerimento_localizacao' => $this->request->getData('requerimento_localizacao'),
                'prazo_placeholder' => $this->request->getData('prazo_placeholder'),
                'ativo' => $this->request->getData('ativo'),
            ]);

            // dd($visita);

            $ativoOriginalValue = $visita->getOriginal('ativo');
            $prazoOriginal = $visita->getOriginal('prazo');

            $visita->validar_prazo = true;

            /**
             * SALVANDO LOG
             */
            $this->loadModel('Logs');
            $dataLog = [
                'modulo_id' => 1,
                'logs_tipo_id' => 2,
                'table_name' => 'visitas',
                'ref' => $visita->id,
                'autor_id' => $this->Auth->user('id'),
                'grupo_id' => $this->Auth->user('grupo_id'),
            ];

            $dataLog = $this->Logs->patchData($dataLog, $visita);
            $log = $this->Logs->newEntity($dataLog);
            $this->Logs->saveOrFail($log);

            if ($this->Visitas->save($visita)) {
                // Pego checklists tb para popular o log.
                $visita = $this->Visitas->get($visita->id, ['contain' => ['Usuarios', 'Checklists', 'Lojas.Cidades']]);

                $responsavel = $this->Visitas->Usuarios->get($this->Auth->user('id'), ['contain' => 'Grupos']);

                if (!$ativoOriginalValue && $visita->ativo) {
                    // Email visita ativada
                    $this->getMailer('Visitas')->send('nova', [$visita, $responsavel]);
                } elseif ($ativoOriginalValue && !$visita->ativo) {
                    // Email visita desativada
                    $this->getMailer('Visitas')->send('cancelada', [$visita, $responsavel]);
                } elseif ($visita->ativo && $prazoOriginal != $visita->prazo) {
                    // Email prazo alterado
                    // não precisa mandar se já mandou o email de nova visita, por
                    // isso a condição está no elseif
                    $this->getMailer('Visitas')->send('prazoAlterado', [$visita, $prazoOriginal, $responsavel]);
                }

                $this->Flash->set('A visita foi alterada.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('Visitas.index', ['action' => 'index']));
            } else {
                $this->Flash->set('A Visita não foi alterada.', ['element' => 'Painel/error']);
            }
        }

        $this->set(compact('visita', 'usuarios', 'breadcrumb'));
    }

    protected function _setPrazo()
    {
        return ($this->request->getData('modelo.prazo_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('modelo.prazo_placeholder')) : null;
    }

    /**
     * Delete method
     *
     * @param string|null $id Visita id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        // Não pode deletar visita que já encerrou, bem obvio né?
        $this->loadModel('Logs');

        $this->request->allowMethod(['post', 'delete']);
        // Contain esses pq uso esses dados no envio do email
        $visita = $this->Visitas->get($this->request->visitaId, ['contain' => ['Usuarios', 'Lojas.Cidades', 'Checklists']]);
        $quemCancelou = $this->Visitas->Usuarios->get($this->Auth->user('id'), ['contain' => 'Grupos']);

        // Só testo senha se possuir dt encerramento
        if ($visita->dt_encerramento && !(new DefaultPasswordHasher())->check($this->request->getData('senha'), $quemCancelou->senha)) {
            throw new BadRequestException("Você não confirmou a sua senha corretamente.");
        }

        $visita->deletado = true;

        if ($this->Visitas->save($visita)) {

            /**
             * SALVANDO LOG
             */
            $dataLog = [
                'modulo_id' => 1,
                'logs_tipo_id' => 1,
                'table_name' => 'visitas',
                'ref' => $visita->id,
                'autor_id' => $this->Auth->user('id'),
                'grupo_id' => $this->Auth->user('grupo_id'),
            ];

            $dataLog = $this->Logs->patchData($dataLog, $visita);
            $log = $this->Logs->newEntity($dataLog);
            $this->Logs->saveOrFail($log);

            // Só manda email de cancelada se estivesse ativo, caso contrario ele já estaria
            // ciente que a visita está cancelada e não mudaria nada pra o auditor
            if ($visita->ativo && !$visita->dt_encerramento) {
                try {
                    $this->getMailer('Visitas')->send('cancelada', [$visita, $quemCancelou]);    
                } catch (\Exception $e) {
                    
                }
            }
        } else {
            throw new BadRequestException("A Visita não foi removida");
        }

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function editPlanosTaticosPreInfos()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Visitas.index', ['action' => 'index']);
        
        $visita = $this->Visitas->get($this->request->visitaId, ['contain' => ['PlanosTaticosPreInfos', 'Lojas', 'Usuarios']]);

        if ($this->request->is(['post', 'patch', 'put'])) {

            if (!$this->request->getData('planos_taticos_pre_info.flag') && $visita->planos_taticos_pre_info) {
                $preInfo = $this->Visitas->PlanosTaticosPreInfos->get($visita->planos_taticos_pre_info->id);
                $this->Visitas->PlanosTaticosPreInfos->deleteOrFail($preInfo);

                // Limpa o form após deletar
                $visita->planos_taticos_pre_info = null;

                $this->Flash->set('Os dados sobre os Planos de Ação da visita foram alterados.', ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Visitas.index', ['action' => 'index']));

            } elseif($this->request->getData('planos_taticos_pre_info.flag')) {

                $visita = $this->Visitas->patchEntity($visita, $this->request->getData());
                $visita->planos_taticos_pre_info->set('grupo_id', $visita->grupo_id);

                if ($this->Visitas->save($visita)) {
                    $this->Flash->set('Os dados sobre os Planos de Ação da visita foram alterados.', ['element' => 'Painel/success']);

                    return $this->redirect($this->breadcrumbRedirect('Visitas.index', ['action' => 'index']));
                } else {
                    $this->Flash->set('Os dados sobre os Planos de Ação da visita não foram alterados.', ['element' => 'Painel/error']);
                }
            }
        }

        $executantes = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'executante plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $solicitantes = $this->Visitas->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'controle plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $this->set(compact('visita', 'executantes', 'solicitantes', 'breadcrumb'));

    }

    public function resultado()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Visitas.index', ['action' => 'index']);

        $visitaId = $this->request->visitaId;

        $visita = $this->Visitas->find()
            ->where([
                'Visitas.id' => $this->request->visitaId
            ])
            ->contain([
                'Usuarios',
                'Respostas' => function($query) {
                    return $query
                        ->contain([
                            'Perguntas',
                            'FotosRequeridas',
                            'AlternativaSelecionada'
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

        $visita->setRespostasFlags();
        $visita->ordenaSetores();
        $visita->setPerguntasNosSetores();

        // Calcula algumas coisas do desempenho
        $visita->setAtingimentos();

        $this->set(compact('visita', 'breadcrumb'));
    }

    public function bim()
    {
        # code...
    }

}
