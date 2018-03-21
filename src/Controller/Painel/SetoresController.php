<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

/**
 * Setores Controller
 *
 * @property \App\Model\Table\SetoresTable $Setores
 */
class SetoresController extends AppController
{
    // Pego o setor para fazer algumas validações no beforeFilter e aproveito para usalos na 
    // action tb para não pegar duas vezes
    public $setor;

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // O id do setor passado como referencia para add e edit deve ter
        // o grupo id igual ao do usuario logado
        // 
        // Se não estiver logado nem testa.. ai ele vai no bloqueia do modulo Auth
        // 
        if ($this->Auth->user() && in_array($this->request->action, ['edit', 'delete'])) {
            // Pego só por id e depois logicamente garanto que atende os requisitos...
            // fica mais rapido que jogar as condições na query
            $this->setor = $this->Setores->get((int)$this->request->setorId);

            if ($this->setor->grupo_id != (int)$this->Auth->user('grupo_id') || $this->setor->deletado) {
                throw new NotFoundException();
            }
        }

        // add e edit compartilham do mesmo template que é o form
        if (in_array($this->request->action, ['add', 'edit'])) {
            $this->viewBuilder()->template('form');
        }

        $this->Security->config('unlockedActions', ['delete']);
    }

    public function index()
    {
        // Breadcrumb
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('Setores.index', [
            'controller' => 'Setores',
            'action' => 'index'
        ]);

        // Pego Vivos e do meu grupo
        $finder = $this->Setores
            ->find('vivos')
            ->find('doMeuGrupo', $this->Auth->user());

        // Filtros da pesquisa
        if ($this->request->query('q')) {
            $q = '%' . $this->request->query('q') . '%';
            $finder->where(['Setores.nome LIKE' => $q]);
        }
        // Se não vier ou se vier algum numero diferente de 0,1 nem faço nada
        if ($this->request->query('status') != '' && in_array($this->request->query('status'), [0, 1])) {
            $finder->where(['Setores.ativo' => (bool)$this->request->query('status')]);
        }

        // Ordeno de primeira por nomes e depois ele pode usar
        // alguns sorts na pagina
        $this->paginate['order'] = ['Setores.nome' => 'asc'];
        // Executo o finder no paginate
        $setores = $this->paginate($finder);

        $this->set(compact('setores'));
    }

    public function add()
    {
        // Adiciono o breadcrumb na session para persistir filtros e sorters no link
        $breadcrumb['index'] = $this->breadcrumbRedirect('Setores.index', ['action' => 'index']);

        // Nova entity vazia para popular o form
        $setor = $this->Setores->newEntity();

        // Postou o form
        if ($this->request->is('post')) {
            // Faço o patch dos dados do form para a entidade vazia, não preciso de me preocupar
            // com mass assign pois já trato issno em entity::Setor
            $setor = $this->Setores->patchEntity($setor, $this->request->getData(), ['entity' => $setor, 'userData' => $this->Auth->user()]);
            
            if ($this->Setores->save($setor)) { 

                $this->Flash->set('O Setor foi salvo.', ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Setores.index', ['action' => 'index']));

            } else {
                $this->Flash->set('O Setor não foi salvo.', ['element' => 'Painel/error']);
            }            

        }

        // Lojas para popular as checkboxes e ele ligar o setor criado as lojas selecionadas automaticamente
        $lojas = $this->Setores->Lojas
            ->find('vivos')
            ->find('ativos')
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('list')
            ->select(['Lojas.id', 'Lojas.nome'])
            ->order(['Lojas.nome']);

        $this->set(compact('setor', 'lojas', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setore id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        // Adiciono o breadcrumb na session para persistir filtros e sorters no link
        $breadcrumb['index'] = $this->breadcrumbRedirect('Setores.index', ['action' => 'index']);

        // Postou o form
        if ($this->request->is(['patch', 'put'])) {

            // Faço o patch dos dados do form para a entidade vazia, não preciso de me preocupar
            // com mass assign pois já trato issno em entity::Setor
            $this->setor = $this->Setores->patchEntity($this->setor, $this->request->getData(), ['entity' => $this->setor, 'userData' => $this->Auth->user()]);

            if ($this->Setores->save($this->setor)) { 

                $this->Flash->set('O Setor foi salvo.', ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Setores.index', ['action' => 'index']));

            } else {
                $this->Flash->set('O Setor não foi salvo.', ['element' => 'Painel/error']);
            }            
        }        

        $this->set([
            'setor' => $this->setor,
            'breadcrumb' => $breadcrumb
        ]);
    }

    public function delete()
    {        
        $this->request->allowMethod(['delete']);

        $this->setor = $this->Setores->patchEntity($this->setor, [], ['entity' => $this->setor, 'userData' => $this->Auth->user()]);
        $this->setor->set('deletado', true);
        
        $this->Setores->saveOrFail($this->setor, ['userData' => $this->Auth->user()]);

        $response = ['message' => 'ok'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }
}
