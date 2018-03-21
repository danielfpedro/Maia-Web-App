<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Event\Event;

/**
 * ChecklistsPerguntas Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasTable $ChecklistsPerguntas
 */
class ChecklistsPerguntasController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        // $this->Auth->allow(['fotosRequeridasUpload']);
    }

}
