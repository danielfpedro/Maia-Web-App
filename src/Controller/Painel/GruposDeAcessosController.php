<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Event\Event;

/**
 * GruposDeAcessos Controller
 *
 * @property \App\Model\Table\GruposDeAcessosTable $grupoDeAcessos
 *
 * @method \App\Model\Entity\GruposDeAcesso[] paginate($object = null, array $settings = [])
 */
class GruposDeAcessosController extends AppController
{

    public function beforeFilter(Event $event)
    {

        // O id do setor passado como referencia para add e edit deve ter
        // o grupo id igual ao do usuario logado
        if (in_array($this->request->action, ['edit', 'delete'])) {
            if (!$this->GruposDeAcessos->exists(['id' => (int)$this->request->grupoDeAcessoId, 'grupo_id' => (int)$this->Auth->user('grupo_id')])) {
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
    public function index()
    {
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('GruposDeAcessos.index', ['controller' => 'GruposDeAcessos', 'action' => 'index']);

        $finder = $this->GruposDeAcessos->todosDoMeuGrupo('all', $this->Auth->user());

        if ($this->request->query('q')) {
            $q = __('%{0}%', $this->request->query('q'));
            $finder->where(['GruposDeAcessos.nome LIKE' => $q]);
        }

        $this->paginate = ['order' => ['GruposDeAcessos.nome']];

        $gruposDeAcessos = $this->paginate($finder);

        $this->set(compact('gruposDeAcessos', 'breadcrumb'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('GruposDeAcessos.index', ['action' => 'index']);

        $grupoDeAcesso = $this->GruposDeAcessos->newEntity();

        if ($this->request->is('post')) {

            $grupoDeAcesso = $this->GruposDeAcessos->patchEntity($grupoDeAcesso, $this->request->getData());

            $grupoDeAcesso->set('grupo_id', $this->Auth->user('grupo_id'));
            $grupoDeAcesso->set('culpado_id', $this->Auth->user('id'));

            if ($this->GruposDeAcessos->save($grupoDeAcesso)) {

                $this->Flash->set('O Grupo de Acesso foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('GruposDeAcessos.index', ['action' => 'index']));
            }
            $this->Flash->set('O Grupo de Acesso não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('grupoDeAcesso', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Grupos De Acesso id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('GruposDeAcessos.index', ['action' => 'index']);

        $grupoDeAcesso = $this->GruposDeAcessos->get($this->request->grupoDeAcessoId);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $grupoDeAcesso = $this->GruposDeAcessos->patchEntity($grupoDeAcesso, $this->request->getData());

            if ($this->GruposDeAcessos->save($grupoDeAcesso)) {

                $this->Flash->set('O Grupo de Acesso foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('GruposDeAcessos.index', ['action' => 'index']));
            }
            $this->Flash->set('O Grupo de Acesso não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('grupoDeAcesso', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Grupos De Acesso id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);

        $grupoDeAcesso = $this->GruposDeAcessos->get($this->request->grupoDeAcessoId);

        if ($this->GruposDeAcessos->delete($grupoDeAcesso)) {
            $this->Flash->set('O Grupo de Acesso foi deletado.', ['element' => 'Painel/success']);
        } else {
            $this->Flash->set('O Grupo de Acesso não foi deletado.', ['element' => 'Painel/error']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
