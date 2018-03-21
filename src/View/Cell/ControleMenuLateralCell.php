<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PainelMenuLateral cell
 */
class ControleMenuLateralCell extends Cell
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

            'Sistema' => [
                [
                    'label' => 'Redes',
                    'icon' => 'building',
                    'url' => ['controller' => 'Grupos', 'action' => 'index'],
                    'internas' => [
                        // ['controller' => 'Usuarios', 'action' => 'add'],
                        // ['controller' => 'Usuarios', 'action' => 'edit'],
                    ]
                ],
                [
                    'label' => 'Usuários',
                    'icon' => 'users',
                    'url' => ['controller' => 'UsuariosControles', 'action' => 'index'],
                    'internas' => [
                        // ['controller' => 'Usuarios', 'action' => 'add'],
                        // ['controller' => 'Usuarios', 'action' => 'edit'],
                    ]
                ]
            ],
        ];

        $this->set(compact('menuItems'));
    }
}
