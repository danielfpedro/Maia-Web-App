<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\BadRequestException;
use Cake\I18n\Time;
use Cake\Collection\Collection;

/**
 * PlanosTaticos Controller
 *
 * @property \App\Model\Table\PlanosTaticosTable $PlanosTaticos
 *
 * @method \App\Model\Entity\PlanosTatico[] paginate($object = null, array $settings = [])
 */
class PlanosTaticosController extends AppController
{

    public function beforeFilter(Event $event)
    {
        // dd($this->Auth->user());
        $meuId = $this->Auth->user('id');

        if (in_array($this->request->action, ['view', 'add', 'edit', 'delete', 'observacaoGeralEdit'])) {
            // Pega pergunta e jogo em uma variavel no escopo da classe pq tres actions vao usar ela
            $this->resposta = $this->PlanosTaticos->Respostas->find()
                ->where(['Respostas.id' => $this->request->respostaId])
                ->contain([
                    'AlternativaSelecionada',
                    'Perguntas' => function($query) {
                        return $query
                            ->contain(['Setores', 'Checklists']);
                    },
                    'PlanosTaticos'
                ])
                ->first();

            if (!$this->resposta || $this->resposta->pergunta->checklist->grupo_id != $this->Auth->user('grupo_id')) {
                throw new NotFoundException();
            }

            // Se naõ for admin eu só posso view coisas que estou como criado por, who ou solicitante
            // Se eu nao sou adm, estou no template view o meu id tem que ser how, solicitante ou criado por
            if (
                !in_array(1, $this->Auth->user('cargos_ids')) &&
                !in_array(4, $this->Auth->user('cargos_ids')) &&
                in_array($this->request->getParam('action'), ['view']) &&
                $this->resposta->plano_tatico->solicitante_id != $meuId &&
                $this->resposta->plano_tatico->who_id != $meuId &&
                $this->resposta->plano_tatico->culpado_id != $meuId
            ) {
                throw new NotFoundException();   
            }
        }

        // add e edit compartilham do mesmo template que é o form
        if (in_array($this->request->action, ['add', 'edit'])) {
            $this->viewBuilder()->template('form');
        }

        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function view()
    {
        $resposta = $this->PlanosTaticos->Respostas->find()
            ->where(['Respostas.id' => $this->request->respostaId])
            ->contain([
                'Perguntas.Setores',
                'AlternativaSelecionada',
                'Visitas.Lojas',
                'PlanosTaticos' => function($query) {
                    return $query
                        ->contain(['Tarefas', 'Solicitantes', 'Whos', 'QuemCriou',]);
                }
            ])
            ->first();

        // Breadcrumb
        $breadcrumb['index'] = $this->breadcrumbRedirect('PlanosTaticos.index', ['action' => 'index']);   

        $finder = $this->PlanosTaticos->Tarefas->find()
            ->contain(['QuemCriou' => function($query) {
                return $query->select(['QuemCriou.id', 'QuemCriou.nome']);
            }])
            ->where([
                'Tarefas.planos_tatico_id' => $resposta->plano_tatico->id
            ])
            ->order(['Tarefas.prazo']);

        $planosTaticosTarefas = $this->paginate($finder);

        $this->set(compact('planosTaticosTarefas', 'breadcrumb'));
        $this->set(['resposta' => $resposta]);
    }

    public function add()
    {
        $breadcrumb = [];

        $resposta = $this->PlanosTaticos->Respostas->find()
            ->where(['Respostas.id' => $this->request->respostaId])
            ->contain([
                'PlanosTaticos',
                'Perguntas.Setores',
                'AlternativaSelecionada'
            ])
            ->first();

        // Novo entity no plano tatico e os valores iniciais
        $resposta->plano_tatico = $this->PlanosTaticos->newEntity();
        $resposta->plano_tatico->set('what', h($this->resposta->pergunta->pergunta));
        // Dissertativa não ter observação selecionada
        if ($resposta->alternativa_selecionada->alternativa) {
            $resposta->plano_tatico->set('why', h($resposta->alternativa_selecionada->alternativa) . PHP_EOL . h($this->resposta->observacao));
        }
        

        $resposta->plano_tatico->culpado_id = $this->Auth->user('id');

        if ($this->request->is(['post', 'put', 'patch'])) {
            
            $this->request->data['plano_tatico']['when_start'] = ($this->request->getData('plano_tatico.when_start_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('plano_tatico.when_start_placeholder')) : null;
            $this->request->data['plano_tatico']['when_end'] = ($this->request->getData('plano_tatico.when_end_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('plano_tatico.when_end_placeholder')) : null;
            $resposta = $this->PlanosTaticos->Respostas->patchEntity($resposta, $this->request->getData(), ['associated' => ['PlanosTaticos']]);


            if ($this->PlanosTaticos->Respostas->save($resposta)) {

                $log = $this->PlanosTaticos->PlanosTaticosLogs->newEntity();

                $log->set('planos_tatico_id', $resposta->plano_tatico->id);
                $log->set('usuario_id', $this->Auth->user('id'));
                $log->set('planos_taticos_logs_tipo_id', 1);

                $this->PlanosTaticos->PlanosTaticosLogs->save($log);

                $this->Flash->set('O Plano Tático foi criado.', ['element' => 'Painel/success']);

                return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'respostaId' => $resposta->id, 'planoTaticoId' => $resposta->plano_tatico->id]);
            }
            $this->Flash->set('O Plano Tático não foi criado.', ['element' => 'Painel/error']);
        }

        $this->loadModel('Usuarios');
        $executantes = $this->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'executante plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $solicitantes = $this->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'controle plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        if ($resposta->plano_tatico->isNew()) {
            $breadcrumb['index'] = $this->breadcrumbRedirect('ChecklistsPerguntasRespostas.index', ['action' => 'index']);   
        }

        $this->set(compact('planoTatico', 'solicitantes', 'executantes', 'breadcrumb'));
        $this->set(['resposta' => $resposta]);
    }

    /**
     * Edit method
     *
     * @param string|null $id Planos Tatico id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {

        $breadcrumb['index'] = $this->breadcrumbRedirect('PlanosTaticos.index', ['action' => 'index']);
        $breadcrumb['view'] = $this->breadcrumbRedirect('PlanosTaticos.view', ['action' => 'view']);

        $resposta = $this->PlanosTaticos->Respostas->find()
            ->where(['Respostas.id' => $this->request->respostaId])
            ->contain([
                'PlanosTaticos',
                'Perguntas.Setores',
                'AlternativaSelecionada'
            ])
            ->first();

        if ($this->request->is(['post', 'put', 'patch'])) {
            
            $this->request->data['plano_tatico']['when_start'] = ($this->request->getData('plano_tatico.when_start_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('plano_tatico.when_start_placeholder')) : null;
            $this->request->data['plano_tatico']['when_end'] = ($this->request->getData('plano_tatico.when_end_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('plano_tatico.when_end_placeholder')) : null;

            //debug($this->request->getData());

            $resposta = $this->PlanosTaticos->Respostas->patchEntity($resposta, $this->request->getData(), ['associated' => ['PlanosTaticos']]);
            
            // dd($resposta);

            if ($this->PlanosTaticos->Respostas->save($resposta)) {
                $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'respostaId' => $resposta->id, 'planoTaticoId' => $resposta->plano_tatico->id]);
            }
            $this->Flash->set('O Plano Tático não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->loadModel('Usuarios');
        $executantes = $this->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'executante plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $solicitantes = $this->Usuarios
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('dosCargos', ['options' => ['cargos' => ['administrador', 'controle plano de ação']]])
            ->find('list')
            ->order(['Usuarios.nome']);

        $this->set(compact('planoTatico', 'solicitantes', 'executantes', 'breadcrumb'));
        $this->set(['resposta' => $resposta]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Planos Tatico id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $planoTatico = $this->PlanosTaticos->get($id);
        if ($this->PlanosTaticos->delete($planosTatico)) {
            $this->Flash->success(__('The planos tatico has been deleted.'));
        } else {
            $this->Flash->error(__('The planos tatico could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function toggleStatus()
    {
        $plano_tatico = $this->PlanosTaticos->get($this->request->planoTaticoId);

        if ($this->request->_csrfToken != $this->request->query('_csrf_token')) {
            throw new NotFoundException();
        }
        
        if (!in_array($this->request->newStatus, [1, 3, 4])) {
            throw new BadRequestException("Status inválido");
        }

        // Log do plano de ação, não confundir com o 
        $log = $this->PlanosTaticos->PlanosTaticosLogs->newEntity();
        $log->set('planos_tatico_id', $plano_tatico->id);
        $log->set('usuario_id', $this->Auth->user('id'));

        switch ((int)$this->request->newStatus) {
            // Aprovado
            case 1:
                $plano_tatico->dt_aprovado = Time::now();
                $plano_tatico->dt_reprovado = null;
                $plano_tatico->dt_cancelamento = null;

                $log->set('planos_taticos_logs_tipo_id', 2);
 
                break;
            // Cancelado, acho que nem existe mais
            // case 2:
            //     $plano_tatico->dt_aprovado = null;
            //     $plano_tatico->dt_reprovado = null;
            //     $plano_tatico->dt_cancelamento = Time::now();

            //     break;
            // Reaberto
            case 3:
                $plano_tatico->dt_aprovado = null;
                $plano_tatico->dt_reprovado = null;
                $plano_tatico->dt_cancelamento = null;

                $log->set('planos_taticos_logs_tipo_id', 4);

                break;
            // Reprovado
            case 4:
                $plano_tatico->dt_aprovado = null;
                $plano_tatico->dt_reprovado = Time::now();
                $plano_tatico->dt_cancelamento = null;

                $log->set('planos_taticos_logs_tipo_id', 3);

                break;
        }

        $this->PlanosTaticos->saveOrFail($plano_tatico);
        $this->PlanosTaticos->PlanosTaticosLogs->saveOrFail($log);

        return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'respostaId' => $this->request->respostaId, 'planoTaticoId' => $plano_tatico->id]);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
    }

    public function index()
    {
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('PlanosTaticos.index', ['controller' => 'PlanosTaticos', 'action' => 'index']);

        $planosTaticosFinder = $this->PlanosTaticos->find()
            ->contain([
                'QuemCriou',
                'Solicitantes',
                'Whos',
                'Tarefas',
                'Respostas' => function($query) {
                    return $query->contain([
                        'Perguntas.Setores',
                        'Visitas' => function($query) {
                            return $query->contain([
                                'Lojas',
                            ]);
                        },
                    ]);
                }
            ])
            ->matching('Respostas.Visitas.Checklists', function($query) {
                return $query->find('dosMeusGruposDeAcessos', $this->Auth->user());
            })
            ->matching('Respostas.Visitas', function($query) {
                return $query->where(['Visitas.grupo_id' => $this->Auth->user('grupo_id')]);
            })
            ->group(['PlanosTaticos.id']);

        // FILTROS
        // 
        if ($this->request->query('visita')) {
            $planosTaticosFinder->innerJoinWith('Respostas.Visitas', function($query) {
                return $query->where(['Visitas.cod' => $this->request->query('visita')]);
            });
        }

        if ($this->request->query('checklist')) {
            $planosTaticosFinder->innerJoinWith('Respostas.Visitas.Checklists', function($query) {
                return $query->where(['Checklists.id IN' => (int)$this->request->query('checklist')]);
            });
        }
        // Filtro Setores
        if ($this->request->query('perguntas')) {
            $planosTaticosFinder->matching('Respostas.Perguntas', function($query) {
                return $query->where(['Perguntas.id IN' => $this->request->query('perguntas')]);
            });
        }
        if ($this->request->query('setores')) {
            $planosTaticosFinder->matching('Respostas.Perguntas.Setores', function($query) {
                return $query->where(['Setores.id IN' => $this->request->query('setores')]);
            });
        }
        //Filtro Lojas
        if ($this->request->query('lojas')) {
            $planosTaticosFinder->matching('Respostas.Visitas.Lojas', function($query) {
                return $query->where(['Lojas.id IN' => $this->request->query('lojas')]);
            });
        }

        if ($this->request->query('status')) {
            switch ($this->request->query('status')) {
                // Não iniciados (Sem tarefas)
                case 1:
                    $planosTaticosFinder->notMatching('Tarefas');
                    break;
                // Em andamento (Deve ter tarefa e não estar nem aprovado e nem reprovado)
                case 2:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where(['dt_aprovado IS' => null, 'dt_reprovado IS' => null]);
                    break;
                // Aguardando aprovação!
                case 3:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas', function($query) {
                            return $query->where(['Tarefas.dt_concluido IS NOT' => null]);
                        })
                        ->where(['dt_aprovado IS' => null, 'dt_reprovado IS' => null]);
                    break;
                // Aprovados (Deve ter tarega e estar aprovado)
                case 4:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where(['dt_aprovado IS NOT' => null]);
                    break;
                case 5:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where([
                            'dt_aprovado IS NOT' => null,
                            'DATE(dt_aprovado) <= DATE(when_end)'
                        ]);
                    break;                    
                // Aprovados com atraso
                case 6:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where([
                            'dt_aprovado IS NOT' => null,
                            'DATE(dt_aprovado) > DATE(when_end)'
                        ]);
                    break;
                // Reprovados
                case 7:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where(['dt_reprovado IS NOT' => null]);
                    break;
                case 8:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where([
                            'dt_reprovado IS NOT' => null,
                            'DATE(dt_reprovado) <= DATE(when_end)'
                        ]);
                    break;
                // Reprovados com atraso
                case 9:
                    $planosTaticosFinder
                        ->innerJoinWith('Tarefas')
                        ->where([
                            'dt_reprovado IS NOT' => null,
                            'DATE(dt_reprovado) > DATE(when_end)'
                        ]);
                    break;
            }
        }

        // FILTRO Criado por
        if ($this->request->query('criado_por')) {
            $planosTaticosFinder->innerJoinWith('QuemCriou', function($query) {
                return $query->where(['QuemCriou.id IN' => $this->request->query('criado_por')]);
            });
        }
        // Filtro Responsavel geral
        if ($this->request->query('responsavel_geral')) {
            $planosTaticosFinder->innerJoinWith('Solicitantes', function($query) {
                return $query->where(['Solicitantes.id IN' => $this->request->query('responsavel_geral')]);
            });
        }
        // Filtro Responsavel geral
        if ($this->request->query('executante')) {
            $planosTaticosFinder->innerJoinWith('Whos', function($query) {
                return $query->where(['Whos.id IN' => $this->request->query('executante')]);
            });
        }

        // DT CRIAÇÃO 
        // DE
        if ($this->request->query('dt_criacao_de')) {
            
            $dtCriacaoDe = Time::createFromFormat('d/m/Y', $this->request->query('dt_criacao_de'));

            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.criado_em) >= ' => $dtCriacaoDe->format('Y-m-d')
                ]);
        }
        // ATE
        if ($this->request->query('dt_criacao_ate')) {
            
            $dtCriacaoAte = Time::createFromFormat('d/m/Y', $this->request->query('dt_criacao_ate'));

            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.criado_em) <= ' => $dtCriacaoAte->format('Y-m-d')
                ]);
        }

        // FILTRO PRAZOS DO PLANO
        // INICIO
        // 
        // DE
        if ($this->request->query('prazo_inicio_de')) {
            
            $prazoInicioDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_inicio_de'));

            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.when_start) >= ' => $prazoInicioDe->format('Y-m-d')
                ]);
        }
        // ATÉ
        if ($this->request->query('prazo_inicio_ate')) {
            
            $prazoInicioAte = Time::createFromFormat('d/m/Y', $this->request->query('prazo_inicio_ate'));
            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.when_start) <= ' => $prazoInicioAte->format('Y-m-d')
                ]);
        }
        // TERMINO
        // 
        // De
        if ($this->request->query('prazo_termino_de')) {
            
            $prazoTerminoDe = Time::createFromFormat('d/m/Y', $this->request->query('prazo_termino_de'));
            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.when_end) >= ' => $prazoTerminoDe->format('Y-m-d')
                ]);
        }
        // ATÉ
        if ($this->request->query('prazo_termino_ate')) {
            
            $prazoTerminoAte = Time::createFromFormat('d/m/Y', $this->request->query('prazo_termino_ate'));

            $planosTaticosFinder
                ->where([
                    'DATE(PlanosTaticos.when_end) <= ' => $prazoTerminoAte->format('Y-m-d')
                ]);
        }

        // IMPORTANTE: Se não for adm somente os que ele é sol, who ou culpado
        if (!in_array(1, $this->Auth->user('cargos_ids')) && !in_array(4, $this->Auth->user('cargos_ids'))) {
            $planosTaticosFinder->where([
                'OR' => [
                    'PlanosTaticos.culpado_id' => $this->Auth->user('id'),
                    'PlanosTaticos.who_id' => $this->Auth->user('id'),
                    'PlanosTaticos.solicitante_id' => $this->Auth->user('id'),
                ]
            ]);
        }

        $this->paginate['order'] = ['PlanosTaticos.criado_em' => 'DESC'];
        $planosTaticos = $this->paginate($planosTaticosFinder);
        // foreach ($planosTaticos as $key => $planoTatico) {
        //     if ($key == 2) {
        //         dd($planoTatico->resposta->visita->cod);
        //     }
            
        // }

        // Preencher filtros
        // Não precisa estar ativo pois eles podem querer ver resultado de nao ativos tb
        // faz todo sentido

        $usuarios = $this->PlanosTaticos->Solicitantes->todosVivosDoMeuGrupo('list', $this->Auth->user());
        $lojas = $this->PlanosTaticos->Solicitantes->Lojas->todosVivosDoMeuGrupo('list', $this->Auth->user());
        $setores = $this->PlanosTaticos->Solicitantes->Lojas->Setores->todosVivosDoMeuGrupo('list', $this->Auth->user());

        $checklists = $this->PlanosTaticos->Respostas->Visitas->Checklists
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('ativos', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('list')
            ->order(['Checklists.nome']);

        $this->set(compact(
            'checklists',
            'planosTaticos',
            'setores',
            'usuarios',
            'auditor',
            'lojas'
        ));
    }

    public function observacaoGeralEdit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('PlanosTaticos.index', ['action' => 'index']);   

        $planoTatico = $this->PlanosTaticos->get($this->request->planoTaticoId, ['select' => ['id', 'observacao_geral']]);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $planoTatico = $this->PlanosTaticos->patchEntity($planoTatico, ['observacao_geral' => $this->request->getData('observacao_geral')]);

            if ($this->PlanosTaticos->save($planoTatico)) {
                $this->Flash->set('A Observação Geral foi salva.', ['element' => 'Painel/success']);

                return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'respostaId' => $this->resposta->id, 'planoTaticoId' => $planoTatico->id]);
            }
            $this->Flash->set('A Observação Geral não foi salva.', ['element' => 'Painel/error']);

        }

        $this->set(['resposta' => $this->resposta]);
        $this->set(compact('planoTatico', 'breadcrumb'));

        $this->viewBuilder()->template('observacao_geral_form');
    }

    public function logsViewModal()
    {
        $this->viewBuilder()->layout('ajax');   

        $planoTatico = $this->PlanosTaticos->get($this->request->planoTaticoId, ['contain' => ['PlanosTaticosLogs' => ['Usuarios', 'PlanosTaticosLogsTipos']]]);

        $logs = (new Collection($planoTatico->planos_taticos_logs))->sortBy('criado_em');

        $this->set(compact('logs'));
    }

}
