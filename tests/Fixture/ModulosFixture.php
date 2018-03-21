<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ModulosFixture
 *
 */
class ModulosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'nome' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'ativo' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'deletado' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'modificado_em' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'nome' => 'Visitas',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
        [
            'id' => 2,
            'nome' => 'Usuários',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
        [
            'id' => 3,
            'nome' => 'Setores',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
        [
            'id' => 4,
            'nome' => 'Lojas',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
        [
            'id' => 5,
            'nome' => 'Modelos de Alternativas',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
        [
            'id' => 6,
            'nome' => 'Questionários',
            'ativo' => true,
            'deletado' => false,
            'criado_em' => '2018-03-05 15:30:03',
            'modificado_em' => '2018-03-05 15:30:03'
        ],
    ];
}
