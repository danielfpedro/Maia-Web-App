<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

/**
 * Estados Controller
 *
 * @property \App\Model\Table\EstadosTable $Estados
 *
 * @method \App\Model\Entity\Estado[] paginate($object = null, array $settings = [])
 */
class EstadosController extends AppController
{

    public function todos()
    {
        $estados = $this->Estados->find();

        $this->set(compact('estados'));
        $this->set('_serialize', ['estados']);
    }
    
}
