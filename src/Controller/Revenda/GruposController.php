<?php
namespace App\Controller\Controle;

use App\Controller\Controle\AppController;

/**
 * Grupos Controller
 *
 * @property \App\Model\Table\GruposTable $Grupos
 *
 * @method \App\Model\Entity\Grupo[] paginate($object = null, array $settings = [])
 */
class GruposController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $finder = $this->Grupos->find();
        $finder
            ->select([
                'Grupos.id',
                'Grupos.nome',
                'Grupos.slug',
                'Grupos.ativo',
                'total_lojas' => $finder->func()->count('Lojas.id'),
                // 'total_usuarios' => $finder->func()->count('Usuarios.id'),
            ])
            ->contain([
                'Segmentos' => function($query) {
                    return $query
                        ->select(['Segmentos.id', 'Segmentos.nome']);
                },
                'QuemGravou'
            ])
            ->leftJoinWith('Lojas', function($query) {
                return $query
                    ->where(['Lojas.deletado' => false]);
            })
            ->leftJoinWith('Usuarios', function($query) {
                return $query
                    ->where(['Usuarios.deletado' => false]);
            })
            ->group('Grupos.id')
            ->order(['Grupos.nome'])
            ->enableAutoFields(true);

        if ($this->request->query('q')) {
            $q = '%' . str_replace(' ', '%', $this->request->query('q')) . '%';
            $finder->where(['Grupos.nome LIKE' => $q]);
        }
        if ($this->request->query('segmento_id')) {
            $finder->where(['Grupos.segmento_id' => (int)$this->request->query('segmento_id')]);
        }

        $grupos = $this->paginate($finder);
        $segmentos = $this->Grupos->Segmentos->todosAtivos('list');

        $this->set(compact('grupos', 'segmentos'));
        $this->set('_serialize', ['grupos']);
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $grupo = $this->Grupos->newEntity();

        if ($this->request->is('post')) {

            $grupo = $this->Grupos->patchEntity($grupo, $this->request->getData());

            if ($this->Grupos->save($grupo)) {
                $this->Flash->set('O Grupo foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->set('O Grupo não foi salvo.', ['element' => 'Painel/error']);
        }

        $segmentos = $this->Grupos->Segmentos->todosAtivos('list');
        $estados = $this->Grupos->Cidades->Estados->find('list');

        $this->set(compact('grupo', 'segmentos', 'estados'));
        $this->viewBuilder()->template('form');
    }

    /**
     * Edit method
     */
    public function edit()
    {

        $grupo = $this->Grupos->get($this->request->grupoId, ['contain' => ['Cidades']]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $grupo = $this->Grupos->patchEntity($grupo, $this->request->getData());

            if ($this->Grupos->save($grupo)) {
                $this->Flash->set('O Grupo foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->referer());
            }
            $this->Flash->set('O Grupo não foi salvo.', ['element' => 'Painel/error']);
        }

        $segmentos = $this->Grupos->Segmentos->todosAtivos('list');
        $estados = $this->Grupos->Cidades->Estados->find('list');

        $this->set(compact('grupo', 'segmentos', 'estados'));

        switch ($this->request->type) {
            case 'navbar':
                $template = 'navbar_form';
                break;
            case 'appNavbar':
                $template = 'app_navbar_form';
                break;
            case 'login':
                $template = 'login_form';
                break;
            default:
                $template = 'form';
                break;
        }
        $this->viewBuilder()->template($template);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $grupo = $this->Grupos->get($this->request->grupoId);
        $grupo->deletado = true;
        if ($this->Grupos->save($grupo)) {
            $this->Flash->set(__('O Grupo foi deletado.'), ['element' => 'Painel/success']);
        } else {
            $this->Flash->set(__('O Grupo não foi deletado.'), ['element' => 'Painel/error']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
