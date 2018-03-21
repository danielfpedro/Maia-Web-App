<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Site Controller
 *
 * @property \App\Model\Table\SiteTable $Site
 */
class SiteController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow();
        $this->viewBuilder()->layout('Site/default');

        parent::beforeFilter($event);
    }

    public function criarConta()
    {
        $this->loadModel('Grupos');
        /**
         * Carrego a entidade pura com lojas e usuarios
         */
        $grupo = $this->Grupos->newEntity(null, [
            'associated' => [
                'Lojas',
                'Usuarios'
            ]
        ]);

        if ($this->request->is('post')) {

            $this->request->data['usuarios'][0]['ativo'] = 1;
            $this->request->data['usuarios'][0]['cargo_id'] = $this->Grupos->Usuarios->cargoInicial;

            $this->request->data['lojas'][0]['ativo'] = 1;

            $grupo = $this->Grupos->patchEntity($grupo, $this->request->data, ['associated' => ['Lojas', 'Usuarios']]);

            if ($this->Grupos->save($grupo)) {
                /**
                 * Pego Usuario que foi criado
                 * @var object
                 */
                $usuario = $grupo->usuarios[0];
                /**
                 * Pego Id da loja para fazer a ligação usuario/loja
                 * @var integer
                 */
                $lojaId = $grupo->lojas[0]->id;
                /**
                 * Preparo os dados para fazer o patch da entidade usuario(que vai ligar a loja no usuario)
                 *
                 * importante dizer o grpo_de_loja_id pois tem uma valiação
                 * que impede ligar lojas de grupos diferentes no usuariom caso
                 * nao tenha esse campo o salvamento vai falhar nessa regra de validação
                 * @var array
                 */
                $data = [
                    'grupo_id' => $usuario->grupo_id,
                    'lojas' => ['_ids' => [$lojaId]]
                ];
                $usuario->dirty('lojas', true);
                $usuario = $this->Grupos->Usuarios->patchEntity($usuario, $data);

                $this->Grupos->Usuarios->save($usuario);
                /**
                 * Se salvar fino jogo pra tela de login
                 */
                $this->Flash->success('O Seu cadastro foi feito com sucesso, você já pode entrar e usar o sistema.');
                return $this->redirect(['controller' => 'Usuarios', 'action' => 'login', 'prefix' => 'painel']);
            }
        }

        $this->set(compact('grupo'));
    }
}
