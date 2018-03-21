<?php
namespace App\Controller\Site;

use App\Controller\Site\AppController;
use Cake\Event\Event;

use Cake\I18n\Time;

/**
 * Site Controller
 *
 * @property \App\Model\Table\SiteTable $Site
 */
class SiteController extends AppController
{

    public function index()
    {
        $now = Time::now();

        $this->set(compact('now'));
    }

}
