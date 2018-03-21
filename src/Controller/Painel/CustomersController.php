<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

/**
 * Customers Controller
 *
 * @property \App\Model\Table\CustomersTable $Customers
 *
 * @method \App\Model\Entity\Customer[] paginate($object = null, array $settings = [])
 */
class CustomersController extends AppController
{

    public $customer;

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);

        // if ($this->Auth->user() && in_array($this->request->action, ['edit', 'delete'])) {

        //     $this->customer = $this->Customers->get((int)$this->request->customerId);

        //     if ($this->customer->user_id != (int)$this->Auth->user('id') || $this->customer->is_alive) {
        //         throw new NotFoundException();
        //     }
        // }

        // add e edit compartilham do mesmo template que é o form
        if (in_array($this->request->action, ['add', 'edit'])) {
            $this->viewBuilder()->template('form');
        }

        if (in_array($this->request->action, ['edit', 'delete'])) {
            $this->customer = $this->Customers->get($this->request->customerId);
        }

        $this->Security->config('unlockedActions', ['create', 'edit', 'delete']);
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
        $this->breadcrumbSet('Lojas.index', ['controller' => 'Customers', 'action' => 'index']);

        $finder = $this->Customers->find()
            ->find('vivos')
            ->find('meus', $this->Auth->user());

        // Filtros
        if ($this->request->query('q')) {
            $q = '%' . $this->request->query('q') . '%';
            $finder->where(['OR' => ['Customers.name LIKE' => $q]]);
        }

        if ($this->request->query('status') != '' && in_array($this->request->query('status'), [0, 1])) {
            $finder->where(['Customers.is_active' => (bool)$this->request->query('status')]);
        }

        $this->paginate['order'] = ['Customers.name' => 'asc'];

        $customers = $this->paginate($finder);

        $this->set(compact('customers'));
    }

    /**
     * View method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $customer = $this->Customers->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('customer', $customer);
        $this->set('_serialize', ['customer']);
    }

    public function add()
    {
        // Adiciono o breadcrumb na session para persistir filtros e sorters no link
        $breadcrumb['index'] = $this->breadcrumbRedirect('Customers.index', ['action' => 'index']);

        // Nova entity vazia para popular o form
        $customer = $this->Customers->newEntity();

        // Postou o form
        if ($this->request->is('post')) {
            // Faço o patch dos dados do form para a entidade vazia, não preciso de me preocupar
            // com mass assign pois já trato issno em entity::Setor
            $customer = $this->Customers->patchEntity($customer, $this->request->getData());
            $customer->set('is_alive', true);
            $customer->set('user_id', 1);

            if ($this->Customers->save($customer)) { 

                $this->Flash->set('O Cliente foi salvo.', ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Clientes.index', ['action' => 'index']));

            } else {
                $this->Flash->set('O Cliente não foi salvo.', ['element' => 'Painel/error']);
            }            

        }

        $this->loadModel('States');
        $states = $this->States->find('list');
        $phonesCompanies = $this->Customers->Phones->PhonesCompanies->find('list');

        $this->set(['customer' => $customer]);
        $this->set(compact('states','phonesCompanies', 'breadcrumb'));
    }

    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Customers.index', ['action' => 'index']);   

        $this->customer = $this->Customers->get($this->request->customerId, ['contain' => ['Phones', 'Addresses']]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $this->customer = $this->Customers->patchEntity($this->customer, $this->request->getData());

            if ($this->Customers->save($this->customer)) {
                $this->Flash->set(__('O Cliente foi salvo com sucesso.'), ['element' => 'Painel/success']);
                return $this->redirect($this->breadcrumbRedirect('Customers.index', ['action' => 'index']));
            }
            $this->Flash->set(__('O Cliente não foi salvo. Por favor, tente novamente.'), ['element' => 'Painel/error']);
        }

        $this->loadModel('States');
        $states = $this->States->find('list');
        $phonesCompanies = $this->Customers->Phones->PhonesCompanies->find('list');

        $this->set(['customer' => $this->customer]);
        $this->set(compact('states','phonesCompanies', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Flash->success(__('The customer has been deleted.'));
        } else {
            $this->Flash->error(__('The customer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
