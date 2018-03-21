<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;

use Cake\Network\Exception\BadRequestException;

use Cake\I18n\Time;

/**
 * ChecklistsPerguntasSetoresOrdem Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasSetoresOrdemTable $ChecklistsPerguntasSetoresOrdem
 */
class ChecklistsPerguntasSetoresOrdemController extends AppController
{
    public function beforeFilter(Event $event)
    {
          $actionsParaValidar = [
            'atualiza'
          ];

         if ($this->Auth->user() && in_array($this->request->action, $actionsParaValidar)) {
             if (!$this->ChecklistsPerguntasSetoresOrdem->Checklists->exists([
                 'id' => (int)$this->request->checklistId,
                 'grupo_id' => (int)$this->Auth->user('grupo_id'),
                 'deletado' => false
             ])) {
                 throw new NotFoundException();
             }
         }

        $this->Security->config('unlockedActions', ['atualiza']);

        parent::beforeFilter($event);
    }

    public function atualiza()
    {
        $checklistId = $this->request->checklistId;

        // Atualiza DT MODIFICADO da Checklist
        // Faço antes pq se falhar pra modificar vai ficar quebrado
        $checklist = $this->ChecklistsPerguntasSetoresOrdem->Checklists->get($checklistId);
        $checklist->dt_modificado = Time::now();
        $this->ChecklistsPerguntasSetoresOrdem->Checklists->saveOrFail($checklist);

        $i = 0;
        foreach ($this->request->data['setores'] as $key => $setorId) {

            $entity = $this->ChecklistsPerguntasSetoresOrdem->find()
                ->where([
                    'checklist_id' => $checklistId,
                    'setor_id' => $setorId
                ])
                ->first();

            if (!$entity) {
                throw new BadRequestException('Um ou mais setores enviados são inválidos');
            }

            $entity->ordem = $i;
            $this->ChecklistsPerguntasSetoresOrdem->saveOrFail($entity);
            $i++;
        }

        $response = ['code' => 200, 'message' => 'success'];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');

    }
}
