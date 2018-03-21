<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SetoresFixture
 *
 */
class SetoresFixture extends TestFixture
{

    public $connection = 'test';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'nome' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'grupo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'criado_por_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'modificado_por_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modificado_em' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'ativo' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null],
        'deletado' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null],
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
            'nome' => 'Setor 01 Ativo Vivo',
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => false
        ],
        [
            'nome' => 'Setor 02 Ativo Morto',
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => true
        ],
        [
            'nome' => 'Setor 03 Inativo Vivo',
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => false,
            'deletado' => false
        ],
        [
            'nome' => 'Setor 04 Inativo Morto',
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => false,
            'deletado' => true
        ],

        // Grupo 02
        [
            'nome' => 'Do Grupo 02',
            'grupo_id' => 2,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => false
        ],
    ];
}
