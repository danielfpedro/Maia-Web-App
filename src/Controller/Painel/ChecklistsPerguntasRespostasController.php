<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\BadRequestException;

use Cake\Collection\Collection;
use Cake\Event\Event;

use Cake\I18n\Time;

/**
 * ChecklistsPerguntasRespostas Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasRespostasTable $ChecklistsPerguntasRespostas
 */
class ChecklistsPerguntasRespostasController extends AppController
{
    public function beforeFilter(Event $event)
    {
        if (in_array($this->request->action, ['toggleCriticoResolvido'])) {
            $resposta = $this->ChecklistsPerguntasRespostas->get($this->request->respostaId, ['contain' => 'Visitas']);

            if (!$resposta || $resposta->visita->grupo_id != (int)$this->Auth->user('grupo_id')) {
                throw new NotFoundException();
            }
        }

        $this->Security->config('unlockedActions', ['toggleCriticoResolvido']);

        $this->Auth->allow(['viewPublic']);

        parent::beforeFilter($event);
    }

    public function viewPublic() {
        $this->_view(true);

        $this->viewBuilder()->layout('Painel/default_respostas_publicas');
    }

    public function view() {
        $this->_view();
    }

    // Usada para resposta privadas quanto publica
    private function _view($isPublic = false) {
        $visitaId = $this->request->visitaId;

        $this->loadModel('Visitas');

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

        if (!$visita || ($isPublic && ($visita->token_visualizar_publico != $this->request->token || !$visita->dt_encerramento))) {
            throw new NotFoundException();
        }

        $visita->setRespostasFlags();
        $visita->ordenaSetores();
        $visita->setPerguntasNosSetores();

        // Calcula algumas coisas do desempenho
        $visita->setAtingimentos();

        $this->set(compact('visita'));
    }


    public function toggleCriticoResolvido()
    {
        $resposta = $this->ChecklistsPerguntasRespostas->get($this->request->respostaId);

        // Vem como string
        $value = ($this->request->getData('value') == 'true') ? Time::now() : null;
        $resposta->critico_resolvido = $value;

        $this->ChecklistsPerguntasRespostas->saveOrFail($resposta);

    }

    public function index()
    {
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('ChecklistsPerguntasRespostas.index', ['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'index']);

        $this->loadModel('Visitas');
        $this->loadModel('ChecklistsPerguntasRespostas');

        $itensCriticosFinder = $this->ChecklistsPerguntasRespostas->find()
            ->contain([
                'AlternativaSelecionada',
                'Perguntas' => function($query) {
                    return $query->contain([
                        'Setores' => function($query) {
                            if ($this->request->query('setores')) {
                                $query->where([
                                    'Setores.id IN' => $this->request->query('setores')
                                ]);
                            }
                            return $query;
                        },
                    ]);
                },
                'PlanosTaticos.Tarefas',
                'FotosRequeridas',
                'Visitas' => function($query) {
                    return $query
                        ->where([
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
                }
            ])
            ->matching('Visitas', function($query) {
                return $query->where(['Visitas.grupo_id' => $this->Auth->user('grupo_id'), 'Visitas.deletado' => false]);
            })
            ->order(['ChecklistsPerguntasRespostas.dt_resposta' => 'desc']);

        if ($this->request->query('visita')) {
            $itensCriticosFinder->innerJoinWith('Visitas', function($query) {
                return $query->where(['Visitas.cod' => $this->request->query('visita')]);
            });
        }

        if ($this->request->query('tipo_resposta')) {
            switch ((int)$this->request->query('tipo_resposta')) {
                case 1:
                    $itensCriticosFinder->matching('AlternativaSelecionada')
                        ->where(['AlternativaSelecionada.item_critico' => true]);
                    break;
                case 2:
                    $itensCriticosFinder->matching('AlternativaSelecionada')
                        ->where(['AlternativaSelecionada.item_critico' => false]);
                    break;
            }
        }

        if ($this->request->query('planos_acao')) {
            switch ((int)$this->request->query('planos_acao')) {
                case 1:
                    $itensCriticosFinder->notMatching('PlanosTaticos')
                        ->group(['ChecklistsPerguntasRespostas.id']);
                    break;
                case 2:
                    $itensCriticosFinder->innerJoinWith('PlanosTaticos')
                        ->group(['ChecklistsPerguntasRespostas.id']);
                    break;
            }
        }

        // Filtro critico
        if ($this->request->query('critico')) {
            switch ((int)$this->request->query('critico')) {
                case 1:
                    $itensCriticosFinder->where(['ChecklistsPerguntasRespostas.critico_resolvido IS NOT' => null]);
                    break;
                case 2:
                    $itensCriticosFinder->where(['ChecklistsPerguntasRespostas.critico_resolvido IS' => null]);
                    break;
            }
        }

        // FILTRO DATA DA RESPOSTA
        if ($this->request->query('resposta_de')) {
            $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('resposta_de'));

            $itensCriticosFinder->where([
                'DATE(ChecklistsPerguntasRespostas.dt_resposta) >= ' => $prazoDe->format('Y-m-d')
            ]);
        }
        if ($this->request->query('resposta_ate')) {
            $prazoDe = Time::createFromFormat('d/m/Y', $this->request->query('resposta_ate'));

            $itensCriticosFinder->where([
                'DATE(ChecklistsPerguntasRespostas.dt_resposta) <= ' => $prazoDe->format('Y-m-d')
            ]);
        }
        

        $itensCriticosFinder->group('ChecklistsPerguntasRespostas.id');
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

    // Já tem uma view que está errado pq ela mostra todas as respostas quando oq deveria
    // mostrar isso seria a Visitas::view
    public function viewModal()
    {
        $resposta = $this->ChecklistsPerguntasRespostas->get($this->request->respostaId, [
            'contain' => [
                'Visitas' => function($query) {
                    return $query->contain([
                        'Usuarios',
                        'Lojas'
                    ]);
                },
                'AlternativaSelecionada',
                'Perguntas' => function($query) {
                    return $query->contain(['Checklists', 'Setores']);
                }
            ]
        ]);

        $this->set(compact('resposta'));

        $this->viewBuilder()->layout('ajax');
    }

}
