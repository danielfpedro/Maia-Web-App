<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;

/**
 * ModelosAlternativas Controller
 *
 * @property \App\Model\Table\ModelosAlternativasTable $ModelosAlternativas
 */
class ModelosAlternativasController extends AppController
{

    public function beforeFilter(Event $event)
    {
        if (in_array($this->request->action, ['edit', 'add'])) {
            $this->alternativas = $this->request->getData('alternativas');
            if ($this->alternativas) {
                $i = 0;
                foreach ($this->alternativas as $key => $alternativa) {
                    $this->alternativas[$key]['ordem'] = $i;
                    $i++;
                }
                unset($this->request->data['alternativas']);
            }

            $this->viewBuilder()->template('form');
        }

        if (in_array($this->request->action, ['edit', 'delete'])) {
            if (!$this->ModelosAlternativas->exists(['id' => (int)$this->request->modelosAlternativaId, 'grupo_id' => (int)$this->Auth->user('grupo_id')])) {
                throw new NotFoundException();
            }
        }
        parent::beforeFilter($event);
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
        $this->breadcrumbSet('ModelosAlternativas.index', ['controller' => 'ModelosAlternativas', 'action' => 'index']);

        $finder = $this->ModelosAlternativas->find();
        $finder
            ->select([
                'ModelosAlternativas.id',
                'ModelosAlternativas.nome',
                'ModelosAlternativas.ativo',
            ])
            ->where([
                'ModelosAlternativas.grupo_id' => (int)$this->Auth->user('grupo_id')
            ])
            ->contain(['AlternativasDosModelos' => function ($query) {
                return $query
                    ->select([
                        'AlternativasDosModelos.id',
                        'AlternativasDosModelos.alternativa',
                        'AlternativasDosModelos.valor',
                        'AlternativasDosModelos.modelos_alternativa_id'
                    ])
                    ->order(['AlternativasDosModelos.ordem']);
            }]);

        if ($this->request->query('q')) {
            $q = '%' . str_replace('%', ' ', $this->request->query('q')) . '%';
            $finder->where(['modelosAlternativas.nome LIKE' => $q]);
        }

        if ($this->request->query('status')) {
            $status = ($this->request->query('status') == 1) ? 1 : 0;
            $finder->where(['modelosAlternativas.ativo' => $status]);
        }

        $modelosAlternativas = $this->paginate($finder);

        $this->set(compact('modelosAlternativas'));
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('ModelosAlternativas.index', ['action' => 'index']);

        $modelosAlternativa = $this->ModelosAlternativas->newEntity(null, [
            'contain' => [
                'AlternativasDosModelos' => function($query) {
                    return $query->order(['AlternativasDosModelos.ordem']);
                }
            ]
        ]);

        if ($this->request->is('post')) {

            $data = $this->request->getData();
            $data['alternativas_dos_modelos'] = $this->alternativas;

            $modelosAlternativa = $this->ModelosAlternativas->patchEntity($modelosAlternativa, $data);

            if ($this->ModelosAlternativas->save($modelosAlternativa)) {

                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $modelosAlternativaParaLog = $this->ModelosAlternativas->get($modelosAlternativa->id, ['contain' => ['QuemGravou']]);

                $dataLog = [
                    'modulo_id' => 5,
                    'logs_tipo_id' => 3,
                    'table_name' => 'modelos_alternativas',
                    'ref' => $modelosAlternativa->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $modelosAlternativaParaLog);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Modelo foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('ModelosAlternativas.index', ['action' => 'index']));
            }
            $this->Flash->set('O Modelo não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('modelosAlternativa', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Modelos Alternativa id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('ModelosAlternativas.index', ['action' => 'index']); 

        $modelosAlternativa = $this
            ->ModelosAlternativas
            ->get($this->request->modelosAlternativaId, [
                'contain' => [
                    'QuemGravou',
                    'AlternativasDosModelos' => function($query) {
                        return $query->order(['AlternativasDosModelos.ordem']);
                    }
                ]
            ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();
            $data['alternativas_dos_modelos'] = $this->alternativas;

            $modelosAlternativa = $this->ModelosAlternativas->patchEntity($modelosAlternativa, $data);

            $modelosAlternativaAntesSalvar = clone $modelosAlternativa;

            if ($this->ModelosAlternativas->save($modelosAlternativa)) {

                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $dataLog = [
                    'modulo_id' => 5,
                    'logs_tipo_id' => 2,
                    'table_name' => 'modelos_alternativas',
                    'ref' => $modelosAlternativa->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $modelosAlternativaAntesSalvar);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Modelo foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('ModelosAlternativas.index', ['action' => 'index']));
            }
            $this->Flash->set('O Modelo não foi salvo.', ['element' => 'Painel/error']);
        }

        $this->set(compact('modelosAlternativa', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Modelos Alternativa id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);
        $modelosAlternativa = $this->ModelosAlternativas->get($this->request->modelosAlternativaId, ['contain' => ['QuemGravou']]);

        if ($this->ModelosAlternativas->delete($modelosAlternativa)) {

            //////////////////
            // SALVANDO LOG //
            //////////////////
            $this->loadModel('Logs');

            $dataLog = [
                'modulo_id' => 5,
                'logs_tipo_id' => 1,
                'table_name' => 'modelos_alternativas',
                'ref' => $modelosAlternativa->id,
                'autor_id' => $this->Auth->user('id'),
                'grupo_id' => $this->Auth->user('grupo_id'),
            ];
            $dataLog = $this->Logs->patchData($dataLog, $modelosAlternativa);
            $log = $this->Logs->newEntity($dataLog);
            $this->Logs->saveOrFail($log);            
            //////////////////
            // FIM SALVANDO LOG //
            //////////////////

            $this->Flash->set('O Modelo não foi deletado.', ['element' => 'Painel/success']);
        } else {
            $this->Flash->set('O Modelo não foi deletado.', ['element' => 'Painel/error']);
        }

        return $this->redirect($this->breadcrumbRedirect('ModelosAlternativas.index', ['action' => 'index']));
    }
}
