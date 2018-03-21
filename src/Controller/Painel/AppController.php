<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller\Painel;

use Cake\Controller\Controller;
use Cake\Event\Event;

use Cake\Network\Exception\NotFoundException;
use Cake\Collection\Collection;
use Cake\Core\Configure;

use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    
    public $helpers = [
        'AssetCompress.AssetCompress',

        'Form' => [
            'className' => 'Bootstrap.Form',
            'columns' => [
                'label' => 2,
                'input' => 6,
                'error' => 4
            ],
            'templates' => [
                'nestingLabel' => '{{input}}<label{{attrs}}>{{text}}</label>',
                'checkboxContainer' => '<div class="checkbox checkbox-primary {{required}}">{{content}}</div>',
            ]
        ],
        'Html' => [
            'className' => 'Bootstrap.Html',
            'templates' => [
                'icon' => '<span class="fa fa-{{type}}{{attrs.class}}"{{attrs}}></span>'
            ]
        ],
    ];

    // Toda a vida do painel ele sem pre vai estar em um grupo, aqui setamos esse
    // grupo que ele esta de acordo com a url
    public $grupo;

    public $paginate = [
        'limit' => 10
    ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        $this->loadComponent('Security', ['blackHoleCallback' => 'forceSSL']);
        $this->loadComponent('Csrf');

        $this->loadComponent('Auth', [
            'authorize' => 'Controller',
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login',
                'prefix' => 'painel'
            ],
            'loginRedirect' => [
                'controller' => 'Customers',
                'action' => 'index',
                'prefix' => 'painel'
            ],
            'authError' => 'Você deve estar logado para acessar esta área.',
            'flash' => [
                'key' => 'auth',
                'element' => 'Painel/inline/default'
            ],
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email'],
                    'userModel' => 'users',
                ]
            ],
            'storage' => [
                'className' => 'Session',
                'key' => 'Auth.Painel'
            ]
        ]);

    }

    public function isAuthorized($user = null)
    {
        return true;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // FORCE HTTPS
        if (!Configure::read('debug')) {
            $this->Security->requireSecure();
        }

        // Layout diferente para estas actions
        if ($this->request->getParam('prefix') == 'painel') {

            // Coloca o Layout "Painel" para tudo exceto para as actions do array abaixo que coloco o Layout "Login"
            $actionsLoginLayout = [
                'login', 'esqueciSenha', 'redefinirSenha'
            ];

            if ($this->request->getParam('controller') == 'Users' && in_array($this->request->getParam('action'), $actionsLoginLayout)) {
                $this->viewBuilder()->layout('Painel/login');
            } else {
                $this->viewBuilder()->layout('Painel/default');
            }
        }

        if ($this->Auth->user() && !($this->request->getParam('controller') == 'Users' && $this->request->getParam('action') == 'logout')) {
            // dd($this->Auth->user());
            // Antes de qualquer coisa, se ele mudar o grupo slug na mão a gente mete o MIM ACHER
            // e fala que a pagina não existe
            // OBs.: Excluímos a action login em que damos um tratamento diferente abaixo
            //
            // //login e redefinir senha pq ele pode clicar no link no email de outro namespace
            // e tem que conseguir entrar

            /**
             * Aqui eu chego a integridade dos dados do usuario logado,
             * por exemplo... se eu não fizesse essa checagem se o usuario logado
             * fosse inativado ele continuaria usando até a sessao expirar.
             */
            // $this->loadModel('Usuarios');

            // $usuario = $this
            //     ->Usuarios
            //     ->find('all')
            //     ->select(['Usuarios.id', 'Usuarios.nome', 'Usuarios.ativo', 'Usuarios.deletado', 'Usuarios.grupo_id'])
            //     ->contain([
            //         'Cargos' => function($query) {
            //             // Na prioridade para mostrar as coisas na logica de prioridades
            //             return $query->order(['Cargos.prioridade']);
            //         },
            //         'GruposDeAcessos'
            //     ])
            //     ->where([
            //         'Usuarios.id' => (int)$this->Auth->user('id'),
            //         'Usuarios.deletado' => false,
            //     ])
            //     ->first();

            // $flashText = '';


            // if (!$usuario) {
            //     $flashText = 'Usuário inexistente.';
            // } else {

            //     $cargosIds = array_map(function($value) {
            //         return $value['id'];
            //     }, $usuario->cargos);

            //     $gruposDeAcessosIds = array_map(function($value) {
            //         return $value['id'];
            //     }, $usuario->grupos_de_acessos);

            //     $usuario->grupos_de_acessos_ids = $gruposDeAcessosIds;
            //     $usuario->cargos_ids = $cargosIds;
                
            //     // $this->Auth->setUser($usuario->toArray());

            //     // Atualizo os carrgos na sessao caso tenha mudado
            //     $this->request->session()->write('Auth.Painel.cargos_ids', $cargosIds);
            //     $this->request->session()->write('Auth.Painel.grupos_de_acessos_ids', $gruposDeAcessosIds);

            //     if ($usuario->ativo != true) {
            //         $flashText = 'O seu usuário foi desativado.';
            //     }
                
            //     if (!$usuario->cargos) {
            //         $flashText = 'O seu usuário não tem nenhum cargo ligado a ele.';
            //     }

            //     // debug($this->grupo->id);
            //     if ($usuario->grupo_id != $this->grupo->id && !in_array($this->request->action, ['login', 'redefinirSenha'])) {
            //         throw new NotFoundException();
            //     }
            //     // Aqui verificarmos que ele tentar acessar o login estando no namespace da grupo redireciona, caso seja o namespace
            //     // de outro grupo deixamos e ele pode logar em outro grupo
            //     // if ($this->request->action == 'login' && $usuario->grupo_id == $this->grupo->id) {
            //     //     return $this->redirect($this->Auth->redirectUrl());
            //     // }
            // }

            // if ($flashText) {
            //     $this->Flash->set($flashText, ['element' => 'Painel/inline/error', 'key' => 'auth']);
            //     return $this->redirect($this->Auth->logout());
            // }
            // // debug($this->request->session()->read('Auth.Painel'));
            // // dd($this->Auth->user());
        }
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    protected function _getGrupoFolder($separator = '/')
    {
        $fileConfig = Configure::read('Files');
        $grupoFolder = implode($fileConfig['grupoFolder'], $separator);
        $grupoFolder = str_replace('{{grupoId}}', $this->grupo->id, $grupoFolder);
        return $grupoFolder;
    }
    protected function _getImagensReferenciaFolder($separator = '/', $checklistId)
    {
        $fileConfig = Configure::read('Files');
        $imagensReferenciaFolder = implode($fileConfig['imagensReferencia'], $separator) . $separator;
        return trim(str_replace('{{checklistId}}', $checklistId, str_replace('{{grupoFolder}}', $this->_getGrupoFolder($separator), $imagensReferenciaFolder)));
    }

    protected function _getRestrictedData($fields, $extra = []) {

        if (!isset($extra['remove'])) {
            $extra['remove'] = [];
        }

        $out = [];
        foreach ($fields as $key => $field) {
            if (!in_array($field, $extra['remove'])) {
                $out[$field] = $this->request->getData($field);
            }
        }

        if (isset($extra['append'])) {
            foreach ($extra['append'] as $toAppend) {
                $out[$toAppend] = $this->request->getData($toAppend);
            }
        }

        return $out;
    }

    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->request->getRequestTarget());
    }

    public function getUserRedirect($user)
    {
        $out = [];
        // Por odem de importancia
        $array = [
            // Admin e Visitas
            '1|5' => [
                'controller' => 'Visitas',
                'action' => 'index'
            ],
            // Auditor
            '2' => [
                'controller' => 'Usuarios',
                'action' => 'alterarSenha'
            ],
            // Controle plano ação
            '4' => [
                'controller' => 'ChecklistsPerguntasRespostas',
                'action' => 'index'
            ],
            // Executante planos taticos
            '3' => [
                'controller' => 'PlanosTaticos',
                'action' => 'index'
            ],
            // Cadastros Gerais
            '6' => [
                'controller' => 'Checklists',
                'action' => 'index'
            ],
        ];
        
        foreach ($array as $key => $value) {
            $exploded = explode('|', $key);
            // dd($user['cargos_ids']);
            foreach ($exploded as $k => $v) {
                if (in_array($v, $user['cargos_ids'])) {
                    $out = $value;
                    break;
                }
            }

            if ($out) {
                break;
            }
        }

        if (!$out) {
            throw new \Exception("Nenhum redirecionar encontrado para os cargos do usuario");
        }

        return $out;
    }

    public function breadcrumbSet($desired, $url)
    {
        $url = array_merge($url, ['?' => $this->request->query()]);
        $this->request->session()->write('Breadcrumb.' . $desired, $url);
    }

    // Se o destino que ele quer existe na sessao eu mando pra la, caso contrario eu mando para o parametro
    // else
    // Obs.: No fluxo normal o 'desired' sempre vai existir. Só não vai existir por exemplo se ele
    // entra na tela de add visita pelo link, ai o endereço do 'index' não vai existir na sessao
    // ai nesse caso a gente redireciona para o 'index' padrao que ele vai informar no 'else'
    public function breadcrumbRedirect($desired, $else)
    {
        if ($this->request->session()->read('Breadcrumb.' . $desired)) {
            return $this->request->session()->read('Breadcrumb.' . $desired);
        }

        return $else;
    }

}
