<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PainelMenuLateral cell
 */
class PainelMenuLateralCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
        $menuItems = [
            'Cadastros' => [
                [
                    'label' => 'Clientes',
                    'icon' => 'users',
                    'url' => ['controller' => 'Customers', 'action' => 'index'],
                ]
            ]
            // 'Auditoria' => [
            //     [
            //         'label' => 'Visitas',
            //         'icon' => 'suitcase',
            //         'url' => ['controller' => 'Visitas', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'Visitas', 'action' => 'add'],
            //             ['controller' => 'Visitas', 'action' => 'edit'],
            //             ['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'view'],
            //         ],
            //         'allow' => [1, 5]
            //     ],
            //     [
            //         'label' => 'Itens Avaliados',
            //         'icon' => 'check',
            //         'url' => ['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'index'],
            //         'internas' => [

            //         ],
            //         'allow' => [1, 4]
            //     ],
            //     [
            //         'label' => 'Planos de Ação',
            //         'icon' => 'bolt',
            //         'url' => ['controller' => 'PlanosTaticos', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'PlanosTaticos', 'action' => 'view'],
            //             ['controller' => 'PlanosTaticos', 'action' => 'add'],  
            //             ['controller' => 'PlanosTaticos', 'action' => 'edit'],
            //             ['controller' => 'PlanosTaticosTarefas', 'action' => 'add'],
            //             ['controller' => 'PlanosTaticosTarefas', 'action' => 'edit'],
            //         ],
            //         'allow' => [1, 3, 4]
            //     ],
            // ],
            // 'Relatórios' => [
            //     [
            //         'label' => 'Por Questionário',
            //         'icon' => 'chart-line',
            //         'url' => ['controller' => 'Relatorios', 'action' => 'porChecklist'],
            //         'internas' => [
            //         ],
            //         'allow' => [1, 5]
            //     ],
            // ],
            // 'Cadastros' => [
            //     [
            //         'label' => 'Notificações por Email',
            //         'icon' => 'at',
            //         'url' => ['controller' => 'GruposDeEmails', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'GruposDeEmails', 'action' => 'add'],
            //             ['controller' => 'GruposDeEmails', 'action' => 'edit'],
            //             // ['controller' => 'Checklists', 'action' => 'edit'],
            //             // ['controller' => 'Checklists', 'action' => 'perguntasForm'],
            //         ],
            //         'allow' => [1, 6]
            //     ],
            //     [
            //         'label' => 'Questionários',
            //         'icon' => 'file-alt',
            //         'url' => ['controller' => 'Checklists', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'Checklists', 'action' => 'add'],
            //             ['controller' => 'Checklists', 'action' => 'edit'],
            //             ['controller' => 'Checklists', 'action' => 'perguntasForm'],
            //         ],
            //         'allow' => [1, 6]
            //     ],
            //     [
            //         'label' => 'Modelos de Alternativas',
            //         'icon' => 'list-ol',
            //         'url' => ['controller' => 'ModelosAlternativas', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'ModelosAlternativas', 'action' => 'add'],
            //             ['controller' => 'ModelosAlternativas', 'action' => 'edit'],
            //         ],
            //         'allow' => [1, 6]
            //     ],
            //     [
            //         'label' => 'Lojas',
            //         'icon' => 'building',
            //         'url' => ['controller' => 'Lojas', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'Lojas', 'action' => 'add'],
            //             ['controller' => 'Lojas', 'action' => 'edit'],
            //         ],
            //         'allow' => [1, 6]
            //     ],
            //     [
            //         'label' => 'Setores',
            //         'icon' => 'clone',
            //         'url' => ['controller' => 'Setores', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'Setores', 'action' => 'add'],
            //             ['controller' => 'Setores', 'action' => 'edit'],
            //         ],
            //         'allow' => [1, 6]
            //     ],
            // ],
            // 'Sistema' => [
            //     [
            //         'label' => 'Grupos de acesso',
            //         'icon' => 'lock',
            //         'url' => ['controller' => 'GruposDeAcessos', 'action' => 'index'],
            //         'internas' => [
            //         ],
            //         'allow' => [1]
            //     ],
            //     [
            //         'label' => 'Usuários',
            //         'icon' => 'users',
            //         'url' => ['controller' => 'Usuarios', 'action' => 'index'],
            //         'internas' => [
            //             ['controller' => 'Usuarios', 'action' => 'add'],
            //             ['controller' => 'Usuarios', 'action' => 'edit'],
            //         ],
            //         'allow' => [1]
            //     ]
            // ],
        ];

        $menu = $this->_nestItems($menuItems);

        $this->set(compact('menu'));
    }

    private function _nestItems($items)
    {
        $menu = [];

        foreach ($items as $menuName => $items) {
            $menu[$menuName] = [];
            foreach ($items as $item) {
                if (1 == 1) {
                    $item['active'] = false;
                    if (
                        (
                            $this->request->controller == $item['url']['controller'] &&
                            $this->request->action == $item['url']['action']
                        ) ||
                        (
                            isset($item['internas']) && in_array(['controller' => $this->request->getParam('controller'), 'action' => $this->request->getParam('action')], $item['internas'])
                        )

                    ) {
                        $item['active'] = true;
                    }
                    unset($item['internas']);
                    $menu[$menuName][] = $item;
                }
            }

            if (!$menu[$menuName]) {
                unset($menu[$menuName]);
            }
        }

        return $menu;
    }

    private function _canPass(array $a, array $b)
    {
        return true;
        $out = false;

        foreach ($a as $value) {
            if (in_array($value, $b)) {
                $out = true;
                break;
            }
        }

        return $out;
    }
}
