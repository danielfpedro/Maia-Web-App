<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

use Cake\Routing\Router;

class GruposController extends AppController
{

  public function getVisual() {
    $grupo = $this->Grupos->get($this->Auth->user('grupo_id'), [
      // Importante mandar só oq preciso para não aparece coisas extras
      'fields' => [
        'id',
        'app_navbar_logo',
        'app_bgcolor' => 'app_navbar_color',
        'app_font_color' => 'app_navbar_font_color',
        'app_statusbar_color',
      ]
    ]);

    $grupo->logo_full_path = Router::url('/', ['fullbase' => true]) . ltrim($grupo->app_navbar_logo_path, '/');

    $this->set(compact('grupo'));
    $this->set('_serialize', 'grupo');
  }

}
