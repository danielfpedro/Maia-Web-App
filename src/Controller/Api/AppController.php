<?php
namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;

use Cake\Core\Configure;

class AppController extends Controller
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');

        $this->loadComponent('Security', ['blackHoleCallback' => 'forceSSL']);
        // $this->loadComponent('Security');

        // dd($this->request->getData());

        $this->loadComponent('Auth', [
            'authorize' => 'Controller',
            'storage' => 'Memory',
            'authenticate' => [
                'Form' => [
                    'finder' => ['apiAuth' => ['validateGrupo' => true, 'grupo' => (isset($this->request->grupo_id)) ? $this->request->grupo_id : null]],
                    'userModel' => 'Usuarios',
                    'fields' => [
                        'username' => 'email',
                        'password' => 'senha'
                    ],
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'userModel' => 'Usuarios',
                    'fields' => [
                        'username' => 'id'
                    ],
                    'finder' => ['apiAuth' => ['validateGrupo' => false]],
                    'parameter' => 'token',

                    // Boolean indicating whether the "sub" claim of JWT payload
                    // should be used to query the Users model and get user info.
                    // If set to `false` JWT's payload is directly returned.
                    'queryDatasource' => true,
                ]
            ],

            'unauthorizedRedirect' => false,
            'checkAuthIn' => 'Controller.initialize',

            // If you don't have a login action in your application set
            // 'loginAction' to false to prevent getting a MissingRouteException.
            'loginAction' => false
        ]);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // FORCE HTTPS
        if (!Configure::read('debug')) {
            $this->Security->requireSecure();
        }
    }

    public function isAuthorized($user = null)
    {
        return true;
    }

    protected function responseSuccess($message = 'ok'){
        return ['message' => $message, 'code' => 200];
    }

    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->request->getRequestTarget());
    }


}
