<?php
namespace App\Controller\Revenda;

use App\Controller\Revenda\AppController;

/**
 * Usuarios Controller
 *
 * @property \App\Model\Table\UsuariosTable $Usuarios
 *
 * @method \App\Model\Entity\Usuario[] paginate($object = null, array $settings = [])
 */
class UsuariosController extends AppController
{

    public function index()
    {
        $finder = $this->Usuarios->find()
            ->where([
                'Usuarios.grupo_id' => $this->request->grupoId,
                'Usuarios.deletado' => false
            ])
            ->contain(['Cargos', 'QuemGravou']);

        // Filtro nome
        if ($this->request->query('cargo_id')) {
            $q = '%' . str_replace(' ', '%', $this->request->query('q')) . '%';
            $finder->where([
                'OR' => [
                    'Usuarios.email LIKE' => $q,
                    'Usuarios.nome LIKE' => $q
                ]
            ]);
        }

        // Filtro Cargo
        if ($this->request->query('cargo_id')) {
            $finder->where(['Usuarios.cargo_id' => (int)$this->request->query('cargo_id')]);
        }

        $usuarios = $this->paginate($finder);
        $cargos = $this->Usuarios->Cargos->todosAtivos('list');
        $grupo = $this->Usuarios->Grupos->get($this->request->grupoId);

        $this->set(compact('usuarios', 'cargos','grupo'));
    }

    public function add()
    {
        $usuario = $this->Usuarios->newEntity();

        if ($this->request->is('post')) {

            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData());

            // Não tem culpado
            $usuario->sem_culpado = true;

            if ($this->Usuarios->save($usuario)) {

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index', 'grupoId' => $this->request->grupoId]);
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $grupo = $this->Usuarios->Grupos->get($this->request->grupoId);
        $cargos = $this->Usuarios->Cargos->todosAtivos('list');

        $this->set(compact('usuario', 'grupo', 'cargos'));

        $this->viewBuilder()->template('form');
    }

    public function edit()
    {
        $usuario = $this->Usuarios->get($this->request->usuarioId);

        if ($this->request->is(['post', 'patch', 'put'])) {

            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData());

            // Não tem culpado
            $usuario->sem_culpado = true;
            
            if ($this->Usuarios->save($usuario)) {

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index', 'grupoId' => $this->request->grupoId]);
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $grupo = $this->Usuarios->Grupos->get($this->request->grupoId);
        $cargos = $this->Usuarios->Cargos->todosAtivos('list');

        $this->set(compact('usuario', 'grupo', 'cargos'));

        $this->viewBuilder()->template('form');
    }

    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);

        /**
         * Deve conter o QuemGravou para montar o log
         * @var Usuario
         */
        $usuario = $this->Usuarios->get($this->request->usuarioId, ['contain' => 'QuemGravou']);

        $usuario->deletado = true;

        if ($this->Usuarios->save($usuario)) {
            $this->Flash->set(__('O Usuário foi deletado.'), ['element' => 'Painel/success']);
        } else {
            $this->Flash->set(__('O Usuário não foi deletado.'), ['element' => 'Painel/error']);
        }

        return $this->redirect(['action' => 'index', 'grupoId' => $this->request->grupoId]);
    }
}
