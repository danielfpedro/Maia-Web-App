<?php

namespace App\Controller\Revenda;

use App\Controller\AppController as ParentAppController;
use Cake\Event\Event;

use Cake\Network\Exception\NotFoundException;

use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends ParentAppController
{
    public $helpers = [
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
            'loginAction' => [
                'controller' => 'RevendasUsuarios',
                'action' => 'login',
                'prefix' => 'revenda'
            ],
            'loginRedirect' => [
                'controller' => 'Grupos',
                'action' => 'index',
                'prefix' => 'revenda'
            ],
            'authError' => 'Você deve estar logado para acessar esta área.',
            'flash' => [
                'key' => 'auth',
                'element' => 'Painel/inline/default'
            ],
            'authenticate' => [
                'Form' => [
                    'finder' => 'authCustom',
                    'fields' => ['username' => 'email', 'password' => 'senha'],
                    'userModel' => 'RevendasUsuarios',
                ]
            ],
            'storage' => ['className' => 'Session', 'key' => 'Auth.Revenda']
        ]);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Layout diferente para estas actions
        if (isset($this->request->params['prefix'])) {

            $actionsLoginLayout = [
                'login',
                'esqueciSenha',
                'redefinirSenha'
            ];
            if ($this->request->params['prefix'] == 'revenda' && $this->request->params['controller'] == 'RevendasUsuarios' && in_array($this->request->params['action'], $actionsLoginLayout)) {
                $this->viewBuilder()->layout('Revenda/login');
            } else {
                $this->viewBuilder()->layout('Revenda/default');
            }
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

}
