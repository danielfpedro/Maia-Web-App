<?php
namespace App\Controller\WebserviceHelpers;

use App\Controller\WebserviceHelpers\AppController;

/**
 * Cidades Controller
 *
 * @property \App\Model\Table\CidadesTable $Cidades
 */
class CidadesController extends AppController
{
    public function todasDoEstado()
    {
        $cidades = $this->Cidades
            ->find('all')
            ->select(['id', 'nome'])
            ->where(['estado_id' => (int)$this->request->query('value')]);

        $this->set(compact('cidades'));
        $this->set('_serialize', ['cidades']);
    }
}
