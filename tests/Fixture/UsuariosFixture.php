<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsuariosFixture
 *
 */
class UsuariosFixture extends TestFixture
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
        'nome' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'email' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'senha' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'modificado_em' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'grupo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'redefinir_senha_token' => ['type' => 'string', 'length' => 400, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'redefinir_senha_email_hash' => ['type' => 'string', 'length' => 400, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'redefinir_senha_timestamp' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'culpado_novo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'quem cadastrou', 'precision' => null, 'autoIncrement' => null],
        'culpado_modificacao_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'ativo' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'deletado' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cargo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_grupo' => ['type' => 'index', 'columns' => ['grupo_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_grupo' => ['type' => 'foreign', 'columns' => ['grupo_id'], 'references' => ['grupos', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'nome' => 'Grupo 01 Ativo Vivo',
            'email' => 'grupo-01-ativo-vivo@gmail.com',
            'senha' => '123mudar',
            'criado_em' => '2018-02-09 16:36:12',
            'modificado_em' => '2018-02-09 16:36:12',
            'grupo_id' => 1,
            'redefinir_senha_token' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_email_hash' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_timestamp' => '2018-02-09 16:36:12',
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'ativo' => true,
            'deletado' => false
        ],
        [
            'nome' => 'Grupo 01 Ativo Morto',
            'email' => 'grupo-01-ativo-morto@gmail.com',
            'senha' => '123mudar',
            'criado_em' => '2018-02-09 16:36:12',
            'modificado_em' => '2018-02-09 16:36:12',
            'grupo_id' => 1,
            'redefinir_senha_token' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_email_hash' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_timestamp' => '2018-02-09 16:36:12',
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'ativo' => true,
            'deletado' => true
        ],
        [
            'nome' => 'Grupo 01 Inativo Vivo',
            'email' => 'grupo-01-inativo-vivo@gmail.com',
            'senha' => '123mudar',
            'criado_em' => '2018-02-09 16:36:12',
            'modificado_em' => '2018-02-09 16:36:12',
            'grupo_id' => 1,
            'redefinir_senha_token' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_email_hash' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_timestamp' => '2018-02-09 16:36:12',
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'ativo' => false,
            'deletado' => false
        ],
        [
            'nome' => 'Grupo 01 Inativo Morto',
            'email' => 'grupo-01-inativo-morto@gmail.com',
            'senha' => '123mudar',
            'criado_em' => '2018-02-09 16:36:12',
            'modificado_em' => '2018-02-09 16:36:12',
            'grupo_id' => 1,
            'redefinir_senha_token' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_email_hash' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_timestamp' => '2018-02-09 16:36:12',
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'ativo' => false,
            'deletado' => true
        ],
        [
            'nome' => 'Grupo 02',
            'email' => 'grupo-02@gmail.com',
            'senha' => '123mudar',
            'criado_em' => '2018-02-09 16:36:12',
            'modificado_em' => '2018-02-09 16:36:12',
            'grupo_id' => 2,
            'redefinir_senha_token' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_email_hash' => 'Lorem ipsum dolor sit amet',
            'redefinir_senha_timestamp' => '2018-02-09 16:36:12',
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'ativo' => true,
            'deletado' => false
        ],
    ];
}
