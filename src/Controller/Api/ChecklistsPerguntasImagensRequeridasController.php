<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

/**
 * ChecklistsPerguntasImagensRequeridas Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasImagensRequeridasTable $ChecklistsPerguntasImagensRequeridas
 */
class ChecklistsPerguntasImagensRequeridasController extends AppController
{

    public function upload()
    {
        dd($this->request->getData());
    }

}
