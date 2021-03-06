<?php
namespace App\Controller\Painel;

use App\Controller\AppController;

/**
 * Phones-companies Controller
 *
 *
 * @method \App\Model\Entity\Phones-company[] paginate($object = null, array $settings = [])
 */
class Phones-companiesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $phonesCompanies = $this->paginate($this->Phones-companies);

        $this->set(compact('phonesCompanies'));
        $this->set('_serialize', ['phonesCompanies']);
    }

    /**
     * View method
     *
     * @param string|null $id Phones Company id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $phonesCompany = $this->Phones-companies->get($id, [
            'contain' => []
        ]);

        $this->set('phonesCompany', $phonesCompany);
        $this->set('_serialize', ['phonesCompany']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $phonesCompany = $this->Phones-companies->newEntity();
        if ($this->request->is('post')) {
            $phonesCompany = $this->Phones-companies->patchEntity($phonesCompany, $this->request->getData());
            if ($this->Phones-companies->save($phonesCompany)) {
                $this->Flash->success(__('The phones company has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The phones company could not be saved. Please, try again.'));
        }
        $this->set(compact('phonesCompany'));
        $this->set('_serialize', ['phonesCompany']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Phones Company id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $phonesCompany = $this->Phones-companies->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $phonesCompany = $this->Phones-companies->patchEntity($phonesCompany, $this->request->getData());
            if ($this->Phones-companies->save($phonesCompany)) {
                $this->Flash->success(__('The phones company has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The phones company could not be saved. Please, try again.'));
        }
        $this->set(compact('phonesCompany'));
        $this->set('_serialize', ['phonesCompany']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Phones Company id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $phonesCompany = $this->Phones-companies->get($id);
        if ($this->Phones-companies->delete($phonesCompany)) {
            $this->Flash->success(__('The phones company has been deleted.'));
        } else {
            $this->Flash->error(__('The phones company could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
