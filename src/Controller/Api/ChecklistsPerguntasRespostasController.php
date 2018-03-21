<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

/**
 * ChecklistsPerguntasRespostas Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasRespostasTable $ChecklistsPerguntasRespostas
 */
class ChecklistsPerguntasRespostasController extends AppController
{
    public function add()
    {
        $entities = $this->Visitas->newEntities($this->request->getData());
        foreach ($entities as $entity) {
            $entity->usuario_id = $this->Auth->user('id');
            $this->ChecklistsPerguntasRespostas->saveOrFail($entity)
        }

        $response = 'ok';

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }
}
