<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LojasFixture
 *
 */
class LojasFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'nome' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'modificado_em' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'grupo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cnpj' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cep' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cidade_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'endereco' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'bairro' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'lat' => ['type' => 'decimal', 'length' => 10, 'precision' => 8, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'lng' => ['type' => 'decimal', 'length' => 11, 'precision' => 8, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'criado_por_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'modificado_por_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'ativo' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'deletado' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
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
            'nome' => 'Loja 01 Ativo Vivo',
            'grupo_id' => 1,
            'cnpj' => '1312312',
            'cep' => '27255555',
            'cidade_id' => 1,
            'endereco' => 'Rua tal',
            'bairro' => 'Sao Gonçalo',
            'lat' => '-22,523275',
            'lng' => '-44,121574',
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => false,
        ],
        [
            'nome' => 'Loja 02 Ativo Morto',
            'grupo_id' => 1,
            'cnpj' => '1312312',
            'cep' => '27255555',
            'cidade_id' => 1,
            'endereco' => 'Rua tal',
            'bairro' => 'Sao Gonçalo',
            'lat' => '-22,523275',
            'lng' => '-44,121574',
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => true
        ],
        [
            'nome' => 'Loja 03 Inativo Vivo',
            'grupo_id' => 1,
            'cnpj' => '1312312',
            'cep' => '27255555',
            'cidade_id' => 1,
            'endereco' => 'Rua tal',
            'bairro' => 'Sao Gonçalo',
            'lat' => '-22,523275',
            'lng' => '-44,121574',
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => false,
            'deletado' => false,
        ],
        [
            'nome' => 'Loja 04 Inativo Morto',
            'grupo_id' => 1,
            'cnpj' => '1312312',
            'cep' => '27255555',
            'cidade_id' => 1,
            'endereco' => 'Rua tal',
            'bairro' => 'Sao Gonçalo',
            'lat' => '-22,523275',
            'lng' => '-44,121574',
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
            'cnpj' => '1312312',
            'cep' => '27255555',
            'cidade_id' => 1,
            'endereco' => 'Rua tal',
            'bairro' => 'Sao Gonçalo',
            'lat' => '-22,523275',
            'lng' => '-44,121574',
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'criado_em' => '2010-10-10 10:10:10',
            'modificado_em' => '2010-10-10 10:10:10',
            'ativo' => true,
            'deletado' => false
        ]
    ];
}
