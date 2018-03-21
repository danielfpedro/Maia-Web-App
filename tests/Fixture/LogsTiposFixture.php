<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LogsTiposFixture
 *
 */
class LogsTiposFixture extends TestFixture
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
        'ativo' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'icon' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'classe de ícone', 'precision' => null, 'fixed' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'modificado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
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
            'nome' => 'Exclusão',
            'ativo' => true,
            'icon' => 'Lorem ipsum dolor sit amet',
            'criado_em' => '2018-03-05 15:33:30',
            'modificado_em' => '2018-03-05 15:33:30'
        ],
        [
            'id' => 2,
            'nome' => 'Edição',
            'ativo' => true,
            'icon' => 'Lorem ipsum dolor sit amet',
            'criado_em' => '2018-03-05 15:33:30',
            'modificado_em' => '2018-03-05 15:33:30'
        ],
        [
            'id' => 3,
            'nome' => 'Adição',
            'ativo' => true,
            'icon' => 'Lorem ipsum dolor sit amet',
            'criado_em' => '2018-03-05 15:33:30',
            'modificado_em' => '2018-03-05 15:33:30'
        ],
    ];
}
