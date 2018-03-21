<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\I18n\Time;
use Cake\Event\Event;

/**
 * PlanosTaticosTarefas Controller
 *
 * @property \App\Model\Table\PlanosTaticosTarefasTable $PlanosTaticosTarefas
 *
 * @method \App\Model\Entity\PlanosTaticosTarefa[] paginate($object = null, array $settings = [])
 */
class PlanosTaticosTarefasController extends AppController
{

    public function beforeFilter(Event $event)
    {

        // add e edit compartilham do mesmo template que é o form
        if (in_array($this->request->action, ['add', 'edit'])) {
            $this->viewBuilder()->template('form');
        }

        parent::beforeFilter($event);
    }

    // Alterar o completo da tarefa para true ou false
    public function completoToggle()
    {
        $tarefa = $this->PlanosTaticosTarefas->get($this->request->tarefaId);
        
        $newValue = ((boolean)$this->request->flag) ? Time::now() : null;

        switch ((int)$this->request->tipo) {
            case 1:
                $tarefa->dt_concluido = $newValue;
                break;
            case 2:
                $tarefa->dt_validado = $newValue;
                break;
        }
        

        $this->PlanosTaticosTarefas->saveOrFail($tarefa);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['PlanosTaticos']
        ];
        $planosTaticosTarefas = $this->paginate($this->PlanosTaticosTarefas);

        $this->set(compact('planosTaticosTarefas'));
        $this->set('_serialize', ['planosTaticosTarefas']);
    }

    /**
     * View method
     *
     * @param string|null $id Planos Taticos Tarefa id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $planosTaticosTarefa = $this->PlanosTaticosTarefas->get($id, [
            'contain' => ['PlanosTaticos']
        ]);

        $this->set('planosTaticosTarefa', $planosTaticosTarefa);
        $this->set('_serialize', ['planosTaticosTarefa']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('PlanosTaticos.index', ['action' => 'index']);   

        $resposta = $this->PlanosTaticosTarefas->PlanosTaticos->Respostas->get($this->request->respostaId, ['contain' => ['PlanosTaticos', 'Perguntas.Setores', 'AlternativaSelecionada']]);

        $planosTaticosTarefa = $this->PlanosTaticosTarefas->newEntity();

        if ($this->request->is('post')) {

            $this->request->data['prazo'] = ($this->request->getData('prazo_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('prazo_placeholder')) : null;

            $planosTaticosTarefa = $this->PlanosTaticosTarefas->patchEntity($planosTaticosTarefa, $this->request->getData());
            
            $planosTaticosTarefa->planos_tatico_id = (int)$this->request->planoTaticoId;
            $planosTaticosTarefa->culpado_id = (int)$this->Auth->user('id');

            if ($this->PlanosTaticosTarefas->save($planosTaticosTarefa)) {
                $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'planoTaticoId' => (int)$this->request->planoTaticoId, 'respostaId' => (int)$this->request->respostaId]);
            }
            $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/error']);
        }


        $this->set(compact('planosTaticosTarefa', 'resposta', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Planos Taticos Tarefa id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('PlanosTaticos.index', ['action' => 'index']);   

        $resposta = $this->PlanosTaticosTarefas->PlanosTaticos->Respostas->get($this->request->respostaId, ['contain' => ['Perguntas.Setores', 'AlternativaSelecionada', 'PlanosTaticos']]);

        $planosTaticosTarefa = $this->PlanosTaticosTarefas->get($this->request->tarefaId);

        if ($this->request->is(['patch', 'put', 'post'])) {

            $this->request->data['prazo'] = ($this->request->getData('prazo_placeholder')) ? Time::createFromFormat('d/m/Y', $this->request->getData('prazo_placeholder')) : null;

            $planosTaticosTarefa = $this->PlanosTaticosTarefas->patchEntity($planosTaticosTarefa, $this->request->getData());
            $planosTaticosTarefa->planos_tatico_id = (int)$this->request->planoTaticoId;

            if ($this->PlanosTaticosTarefas->save($planosTaticosTarefa)) {
                $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'planoTaticoId' => $resposta->plano_tatico->id, 'respostaId' => (int)$this->request->respostaId]);
            }
            $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/error']);
        }


        $this->set(compact('planosTaticosTarefa', 'resposta', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Planos Taticos Tarefa id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $resposta = $this->PlanosTaticosTarefas->PlanosTaticos->Respostas->get($this->request->respostaId, ['contain' => ['Perguntas', 'AlternativaSelecionada', 'PlanosTaticos']]);

        $planosTaticosTarefa = $this->PlanosTaticosTarefas->get((int)$this->request->tarefaId);

        if ($this->PlanosTaticosTarefas->delete($planosTaticosTarefa)) {
            $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/success']);

            return $this->redirect(['controller' => 'PlanosTaticos', 'action' => 'view', 'planoTaticoId' => $resposta->plano_tatico->id, 'respostaId' => (int)$this->request->respostaId]);
        }
        $this->Flash->set('O Plano Tático foi salvo.', ['element' => 'Painel/error']);

        return $this->redirect(['action' => 'index']);
    }
}
