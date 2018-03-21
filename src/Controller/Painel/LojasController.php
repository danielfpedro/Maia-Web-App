<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;

use Cake\Network\Exception\NotFoundException;

/**
 * Lojas Controller
 *
 * @property \App\Model\Table\LojasTable $Lojas
 */
class LojasController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if ($this->Auth->user() && in_array($this->request->action, ['edit', 'delete'])) {

            $this->loja = $this->Lojas->get((int)$this->request->lojaId, ['contain' => ['Cidades', 'Setores']]);

            if ($this->loja->grupo_id != (int)$this->Auth->user('grupo_id') || $this->loja->deletado) {
                throw new NotFoundException();
            }
        }

        // add e edit compartilham do mesmo template que é o form
        if (in_array($this->request->action, ['add', 'edit'])) {
            $this->viewBuilder()->template('form');
        }

        $this->Security->config('unlockedActions', ['delete']);
    }

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
        $this->breadcrumbSet('Lojas.index', ['controller' => 'Lojas', 'action' => 'index']);

        $finder = $this->Lojas->find()
            ->find('vivos')
            ->find('doMeuGrupo', $this->Auth->user())
            ->contain([
                'Cidades',
                'Setores' => function($query) {
                    return $query->order(['Setores.nome']);
                }
            ]);

        // Filtros
        if ($this->request->query('q')) {
            $q = '%' . $this->request->query('q') . '%';
            $finder->where(['OR' => ['Lojas.nome LIKE' => $q, 'Lojas.endereco LIKE' => $q, 'Lojas.bairro LIKE' => $q]]);
        }
        if ($this->request->query('setores')) {
            $finder->innerJoinWith('Setores', function($query) {
                return $query
                    ->where(['Setores.id IN' => $this->request->query('setores')]);
            })
            ->group('Lojas.id');
        }

        if ($this->request->query('status') != '' && in_array($this->request->query('status'), [0, 1])) {
            $finder->where(['Lojas.ativo' => (bool)$this->request->query('status')]);
        }

        $this->paginate['order'] = ['Lojas.nome' => 'asc'];

        $lojas = $this->paginate($finder);

        $setores = $this->Lojas->Setores->find()
            ->find('vivos')
            ->find('ativos')
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('list')
            ->order(['Setores.nome']);

        $this->set(compact('lojas', 'setores'));
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Lojas.index', ['action' => 'index']);

        $loja = $this->Lojas->newEntity();

        if ($this->request->is('post')) {

            $loja = $this->Lojas->patchEntity($loja, $this->request->data, ['entity' => $loja, 'userData' => $this->Auth->user()]);

            if ($this->Lojas->save($loja)) {
                $this->Flash->set(__('A loja foi salva.'), ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Lojas.index', ['action' => 'index']));
            }
            $this->Flash->set(__('A loja não foi salva.'), ['element' => 'Painel/error']);
        }

        $estados = $this->Lojas->Cidades->Estados->find('list')
            ->order(['Estados.nome']);

        $setores = $this->Lojas->Setores->find()
            ->find('vivos')
            ->find('ativos')
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('list')
            ->order(['Setores.nome']);

        $this->set(compact('loja', 'estados', 'setores', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Loja id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Lojas.index', ['action' => 'index']);   

        if ($this->request->is(['patch', 'post', 'put'])) {

            $this->loja = $this->Lojas->patchEntity($this->loja, $this->request->data, ['entity' => $this->loja, 'userData' => $this->Auth->user()]);

            if ($this->Lojas->save($this->loja)) {
                $this->Flash->set(__('A loja foi salva com sucesso.'), ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Lojas.index', ['action' => 'index']));
            }
            $this->Flash->set(__('A loja não foi salva. Por favor, tente novamente.'), ['element' => 'Painel/error']);
        }

        $estados = $this->Lojas->Cidades->Estados->find('list');
        $setores = $this->Lojas->Setores->find()
            ->find('vivos')
            ->find('ativos')
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('list')
            ->order(['Setores.nome']);

        $this->set(['loja' => $this->loja]);
        $this->set(compact('estados', 'setores', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Loja id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $this->request->allowMethod(['delete']);
        $this->loja->deletado = true;

        $this->Lojas->saveOrFail($this->loja);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }
}
