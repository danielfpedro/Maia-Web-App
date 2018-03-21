<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GruposFixture
 *
 */
class GruposFixture extends TestFixture
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
        'ativo' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'slug' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'navbar_logo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'navbar_color' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'navbar_logo_width' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'navbar_logo_margin_top' => ['type' => 'integer', 'length' => 255, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'navbar_font_color' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'login_logo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'login_logo_width' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'app_navbar_logo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'app_navbar_color' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'app_navbar_font_color' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'app_statusbar_color' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'logo_email' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'segmento_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'culpado_novo_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'culpado_modificacao_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'criado_em' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'nome_fantasia' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cnpj' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cep' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cidade_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'endereco' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'bairro' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'inscricao_estadual' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'razao_social' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'email_financeiro' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
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
            'id' => 1,
            'nome' => 'Grupo 01',
            'ativo' => true,
            'slug' => 'grupo-01',
            'navbar_logo' => 'Lorem ipsum dolor sit amet',
            'navbar_color' => 'Lorem ipsum dolor sit amet',
            'navbar_logo_width' => 1,
            'navbar_logo_margin_top' => 1,
            'navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'login_logo' => 'Lorem ipsum dolor sit amet',
            'login_logo_width' => 1,
            'app_navbar_logo' => 'Lorem ipsum dolor sit amet',
            'app_navbar_color' => 'Lorem ipsum dolor sit amet',
            'app_navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'app_statusbar_color' => 'Lorem ipsum dolor sit amet',
            'logo_email' => 'Lorem ipsum dolor sit amet',
            'segmento_id' => 1,
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'criado_em' => '2018-01-12 15:23:33',
            'nome_fantasia' => 'Lorem ipsum dolor sit amet',
            'cnpj' => 'Lorem ipsum dolor sit amet',
            'cep' => 'Lorem ipsum dolor sit amet',
            'cidade_id' => 1,
            'endereco' => 'Lorem ipsum dolor sit amet',
            'bairro' => 'Lorem ipsum dolor sit amet',
            'inscricao_estadual' => 'Lorem ipsum dolor sit amet',
            'razao_social' => 'Lorem ipsum dolor sit amet',
            'email_financeiro' => 'Lorem ipsum dolor sit amet'
        ],
        [
            'id' => 2,
            'nome' => 'Grupo 02',
            'ativo' => true,
            'slug' => 'Lorem ipsum dolor sit amet',
            'navbar_logo' => 'Lorem ipsum dolor sit amet',
            'navbar_color' => 'Lorem ipsum dolor sit amet',
            'navbar_logo_width' => 1,
            'navbar_logo_margin_top' => 1,
            'navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'login_logo' => 'Lorem ipsum dolor sit amet',
            'login_logo_width' => 1,
            'app_navbar_logo' => 'Lorem ipsum dolor sit amet',
            'app_navbar_color' => 'Lorem ipsum dolor sit amet',
            'app_navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'app_statusbar_color' => 'Lorem ipsum dolor sit amet',
            'logo_email' => 'Lorem ipsum dolor sit amet',
            'segmento_id' => 1,
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'criado_em' => '2018-01-12 15:23:33',
            'nome_fantasia' => 'Lorem ipsum dolor sit amet',
            'cnpj' => 'Lorem ipsum dolor sit amet',
            'cep' => 'Lorem ipsum dolor sit amet',
            'cidade_id' => 1,
            'endereco' => 'Lorem ipsum dolor sit amet',
            'bairro' => 'Lorem ipsum dolor sit amet',
            'inscricao_estadual' => 'Lorem ipsum dolor sit amet',
            'razao_social' => 'Lorem ipsum dolor sit amet',
            'email_financeiro' => 'Lorem ipsum dolor sit amet'
        ],
        [
            'id' => 3,
            'nome' => 'Grupo Inativo',
            'ativo' => false,
            'slug' => 'Lorem ipsum dolor sit amet',
            'navbar_logo' => 'Lorem ipsum dolor sit amet',
            'navbar_color' => 'Lorem ipsum dolor sit amet',
            'navbar_logo_width' => 1,
            'navbar_logo_margin_top' => 1,
            'navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'login_logo' => 'Lorem ipsum dolor sit amet',
            'login_logo_width' => 1,
            'app_navbar_logo' => 'Lorem ipsum dolor sit amet',
            'app_navbar_color' => 'Lorem ipsum dolor sit amet',
            'app_navbar_font_color' => 'Lorem ipsum dolor sit amet',
            'app_statusbar_color' => 'Lorem ipsum dolor sit amet',
            'logo_email' => 'Lorem ipsum dolor sit amet',
            'segmento_id' => 1,
            'culpado_novo_id' => 1,
            'culpado_modificacao_id' => 1,
            'criado_em' => '2018-01-12 15:23:33',
            'nome_fantasia' => 'Lorem ipsum dolor sit amet',
            'cnpj' => 'Lorem ipsum dolor sit amet',
            'cep' => 'Lorem ipsum dolor sit amet',
            'cidade_id' => 1,
            'endereco' => 'Lorem ipsum dolor sit amet',
            'bairro' => 'Lorem ipsum dolor sit amet',
            'inscricao_estadual' => 'Lorem ipsum dolor sit amet',
            'razao_social' => 'Lorem ipsum dolor sit amet',
            'email_financeiro' => 'Lorem ipsum dolor sit amet'
        ]
    ];
}
