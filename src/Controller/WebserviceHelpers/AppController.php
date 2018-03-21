<?php
namespace App\Controller\WebserviceHelpers;

use App\Controller\AppController as ParentAppController;
use Cake\Event\Event;

class AppController extends ParentAppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
    }


    protected function responseSuccess($message = 'ok'){
        return ['message' => $message, 'code' => 200];
    }

}
