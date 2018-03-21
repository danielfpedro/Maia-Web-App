<?php
namespace App\Controller\Revenda;

use App\Controller\Revenda\AppController;

/**
 * UsuariosControles Controller
 *
 * @property \App\Model\Table\UsuariosControlesTable $UsuariosControles
 *
 * @method \App\Model\Entity\UsuariosControle[] paginate($object = null, array $settings = [])
 */
class RevendasUsuariosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $finder = $this->UsuariosControles->find();

        if ($this->request->query('q')) {
            $q = '%' . str_replace(' ', '%', $this->request->query('q')) . '%';

            $finder->where([
                'OR' => [
                    'UsuariosControles.nome LIKE' => $q,
                    'UsuariosControles.email LIKE' => $q,
                ]
            ]);
        }

        $usuarios = $this->paginate($finder);

        $this->set(compact('usuarios'));
        $this->set('_serialize', ['usuarios']);
    }

    /**
     * View method
     *
     * @param string|null $id Usuarios Controle id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usuariosControle = $this->UsuariosControles->get($id, [
            'contain' => []
        ]);

        $this->set('usuariosControle', $usuariosControle);
        $this->set('_serialize', ['usuariosControle']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usuario = $this->UsuariosControles->newEntity();

        if ($this->request->is('post')) {

            $usuario = $this->UsuariosControles->patchEntity($usuario, $this->request->getData());

            if ($this->UsuariosControles->save($usuario)) {

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('usuario'));

        $this->viewBuilder()->template('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuarios Controle id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $usuario = $this->UsuariosControles->get($this->request->usuarioId);

        if ($this->request->is(['put', 'patch', 'post'])) {

            $usuario = $this->UsuariosControles->patchEntity($usuario, $this->request->getData());

            if ($this->UsuariosControles->save($usuario)) {

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('usuario'));

        $this->viewBuilder()->template('form');
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->set('Combinação email/senha incorreta.', ['element' => 'Painel/inline/error', 'key' => 'auth']);
            }
        }
    }
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

}
