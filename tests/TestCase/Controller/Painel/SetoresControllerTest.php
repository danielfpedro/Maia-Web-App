<?php 

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Painel\Data;
use App\Test\TestCase\Controller\HelperController;

use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;

class SetoresControllerTest extends HelperController
{
    public $fixtures = [
        'app.setores',
        'app.cargos',
        'app.cargos_usuarios',
        'app.grupos',
        'app.grupos_de_acessos',
        'app.grupos_de_acessos_usuarios',
        'app.logs',
        'app.logs_tipos',
        'app.lojas',
        'app.modulos',
        'app.usuarios',
    ];

    private function _setAuthSession()
    {
	    $this->session([
	        'Auth' => [
	            'Painel' => [
	           		'id' => Data::MY_ID,
	                'grupo_id' => Data::MY_GRUPO,
	                'cargos_ids' => [1]
	            ]
	        ]
	    ]);
    }

    public function setUp() {
    	parent::setUp();
    	$this->_setAuthSession();
    }

    public function tearDown()
    {   
        TableRegistry::clear();
        parent::tearDown();
    }

    // INDEX
    public function testIndexUnauthenticated()
    {
        $this->session(['Auth' => null]);

        $this->get('/painel/grupo-01/setores');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/setores']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testIndex()
    {
        $this->get('/painel/grupo-01/setores');
        $this->assertResponseOk();

        $setores = $this->viewVariable('setores');
        $this->assertEquals(2, $setores->count());
        foreach ($setores as $setor) {
            $this->assertFalse($setor->deletado);
        }
    }

    // FILTROS
    // Nome
    public function testIndexFiltroNome()
    {
    	// Existe
        $this->get('/painel/grupo-01/setores?q=setor+01+ativo+vivo');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(1, $setores->count());
        // Nome que não existe
        $this->get('/painel/grupo-01/setores?q=chicorita');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(0, $setores->count());
    }
    // Status
    public function testIndexFiltroStatus()
    {
    	// Ativos
        $this->get('/painel/grupo-01/setores?status=1');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(1, $setores->count());
        // Inativos
        $this->get('/painel/grupo-01/setores?status=0');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(1, $setores->count());
        // Se passar inválido retorna tudo
        $this->get('/painel/grupo-01/setores?status=17805054046');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(2, $setores->count());
    }
    // Combinados
    public function testIndexFiltroTodos()
    {
        $this->get('/painel/grupo-01/setores?q=setor+01+ativo+vivo&status=1');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(1, $setores->count());

        $this->get('/painel/grupo-01/setores?q=setor+01+ativo+vivo&status=0');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(0, $setores->count());

        $this->get('/painel/grupo-01/setores?q=setor+01+inativo+vivo&status=1');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');
        $this->assertEquals(0, $setores->count());
    }


    // ADD
    public function testAddUnAuthenticated()
    {
        $this->session(['Auth' => null]);
        $this->get('/painel/grupo-01/setores/adicionar');

        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/setores/adicionar']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testAdd()
    {
        $this->get('/painel/grupo-01/setores/adicionar');
        $this->assertResponseOk();
    }
    public function testAddPostData()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $data = ['nome' => 'dsa980ipokdsa980ipokldas', 'ativo' => true];
        $this->post('/painel/grupo-01/setores/adicionar', $data);
        $this->assertResponseSuccess();

        $setores = TableRegistry::get('Setores');

        $query = $setores->find('all')->where(['nome' => $data['nome']])->first();
        
        $this->assertNotNull($query);

        $result = ['nome' => $query->nome, 'ativo' => $query['ativo']];
        $this->assertEquals($data, $result);
    }

    // EDIT
    public function testEditUnauthenticated()
    {
        $this->session(['Auth' => null]);
        $this->get('/painel/grupo-01/setores/1/editar');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/setores/1/editar']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }

    public function testEdit()
    {
        // Setor existente
        $this->get('/painel/grupo-01/setores/1/editar');
        $this->assertResponseSuccess();

        // Setor inexistente
        $this->get('/painel/grupo-01/setores/17887978987/editar');
        $this->assertResponseCode(404);
        // Setor deletado
        $this->get('/painel/grupo-01/setores/2/editar');
        $this->assertResponseCode(404);
        // Setor de outro grupo
        $this->get('/painel/grupo-01/setores/5/editar');
        $this->assertResponseCode(404);
    }

    public function testEditPutData()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        // // Agora edita
        $data = [
            'nome' => 'a34245432342121232144515442',
            'ativo' => false,
        ];
        $this->put('/painel/grupo-01/setores/1/editar', $data);
        $this->assertResponseSuccess();

        $setores = TableRegistry::get('Setores');
        $result = $setores->get(1);
        $resultData = ['nome' => $result->nome, 'ativo' => $result->ativo];
        $this->assertEquals($data, $resultData);
    }

    // DELETAR
    public function testDeleteUnauthenticated()
    {
        $this->session(['Auth' => null]);
        $this->get('/painel/grupo-01/setores/1/deletar');
        $this->assertResponseCode(302);
    }
    public function testDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        // Metodos nao permitidos
        HelperController::assertNotThisMethods(['get', 'post', 'put', 'patch'], '/painel/grupo-01/setores/1/deletar.json');

        // Setor inexistente
        $this->delete('/painel/grupo-01/setores/1321312/deletar.json');
        $this->assertResponseCode(404);
        // Setor de outro grupo
        $this->delete('/painel/grupo-01/setores/5/deletar.json');
        $this->assertResponseCode(404);
        // Não pode acessar o que ja está deletado
        $this->delete('/painel/grupo-01/setores/2/deletar.json');
        $this->assertResponseCode(404);

        $this->delete('/painel/grupo-01/setores/1/deletar.json');
        $this->assertResponseOk();
        $setores = TableRegistry::get('Setores');
        $result = $setores->get(1);
        $this->assertTrue($result->deletado);
    }
}