<?php
namespace App\Controller\WebserviceHelpers;

use App\Controller\WebserviceHelpers\AppController;

/**
 * Enderecos Controller
 *
 * @property \App\Model\Table\EnderecosTable $Enderecos
 */
class EnderecosController extends AppController
{
    public function todosPeloCep()
    {
        $cep = str_replace('-', '', $this->request->query('cep'));

        $result = $this->Enderecos->find('all')
            ->where(['cep' => $cep])
            ->contain([
                'Cidades',
                'Bairros'
            ])
            ->first();

        $this->set(compact('result'));
        $this->set('_serialize', ['result']);
    }
}
