<?php
namespace App\Test\TestCase\Controller\Painel;

use App\Controller\Painel\LojasController;
use App\Test\TestCase\Painel\Data;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Painel\LojasController Test Case
 */
class LojasControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
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
        'app.lojas_setores',
        'app.modulos',
        'app.usuarios',
        'app.cidades',
        'app.estados',
    ];

    public function setUp() {
        parent::setUp();

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

    public function tearDown()
    {   
        TableRegistry::clear();
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexUnauthenticated()
    {
        $this->session(['Auth' => null]);

        $this->get('/painel/grupo-01/lojas');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/lojas']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testIndex()
    {
        $this->get('/painel/grupo-01/lojas');
        $this->assertResponseOk();
        
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(2, $lojas->count());
        foreach ($lojas as $loja) {
            $this->assertFalse($loja->deletado);
        }

        $setores = $this->viewVariable('setores');
        $this->assertEquals(1, $setores->count());

        $setoresTable = TableRegistry::get('Setores');
        foreach ($setores as $id => $setorNome) {
            $entity = $setoresTable->get($id);
            $this->assertFalse($entity->deletado);
            $this->assertTrue($entity->ativo);
        }
    }

    public function testIndexFiltroQ()
    {
        // Existe
        $this->get('/painel/grupo-01/lojas?q=loja+01+ativo+vivo');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        
        $this->assertEquals(1, $lojas->count());

        // Nome que não existe
        $this->get('/painel/grupo-01/lojas?q=chicorita');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(0, $lojas->count());
    }
    // Status
    public function testIndexFiltroStatus()
    {
        // Ativos
        $this->get('/painel/grupo-01/lojas?status=1');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(1, $lojas->count());

        // Inativos
        $this->get('/painel/grupo-01/lojas?status=0');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(1, $lojas->count());

        // Se passar inválido retorna tudo
        $this->get('/painel/grupo-01/lojas?status=17805054046');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(2, $lojas->count());
    }
    // Setores
    // 
    public function testIndexFiltroSetores()
    {
        $this->get('/painel/grupo-01/lojas?setores[]=1');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(2, $lojas->count());

        $this->get('/painel/grupo-01/lojas?setores[]=3');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(1, $lojas->count());


        $this->get('/painel/grupo-01/lojas?setores[]=1&setores[]=3');
        $this->assertResponseOk();
        $lojas = $this->viewVariable('lojas');
        $this->assertEquals(2, $lojas->count());
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAddUnauthenticated()
    {
        $this->session(['Auth' => null]);

        $this->get('/painel/grupo-01/lojas/adicionar');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/lojas/adicionar']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testAdd()
    {
        $this->get('/painel/grupo-01/lojas/adicionar');
        $this->assertResponseOk();
        $setores = $this->viewVariable('setores');

        $setoresTable = TableRegistry::get('Setores');
        foreach ($setores as $id => $setorNome) {
            $entity = $setoresTable->get($id);
            $this->assertFalse($entity->deletado);
            $this->assertTrue($entity->ativo);
        }
    }
    public function testAddPostData()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $lojasTable = TableRegistry::get('Lojas');
        $data = $lojasTable->get(1)->toArray();

        $data['nome'] = 'Chicorita';
        $data['setores']['_ids'] = [1];

        $this->post('/painel/grupo-01/lojas/adicionar', $data);
        $this->assertResponseSuccess();

        $chicorita = $lojasTable->find()
            ->contain(['Setores'])
            ->where(['Lojas.nome' => 'Chicorita'])
            ->first();

        $this->assertNotNull($chicorita);
        $this->assertEquals(1, count($chicorita->setores));

        $this->assertEquals(1, $chicorita->setores[0]->id);

        unset($chicorita->id);
        unset($chicorita->criado_em);
        unset($chicorita->modificado_em);
        unset($chicorita->setores);

        unset($data['id']);
        unset($data['criado_em']);
        unset($data['modificado_em']);
        unset($data['setores']);

        $this->assertEquals($data, $chicorita->toArray());
    }

    public function testEditUnauthenticated()
    {
        $this->session(['Auth' => null]);

        $this->get('/painel/grupo-01/lojas/1/editar');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/lojas/1/editar']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testEdit()
    {
        $this->get('/painel/grupo-01/lojas/1/editar');
        $this->assertResponseOk();

        $loja = $this->viewVariable('loja');
        $lojasTable = TableRegistry::get('Lojas');
        $lojaEntity = $lojasTable->get(1, ['contain' => ['Cidades', 'Setores']]);
        $this->assertEquals($lojaEntity, $loja);

        $setores = $this->viewVariable('setores');

        $setoresTable = TableRegistry::get('Setores');
        foreach ($setores as $id => $setorNome) {
            $entity = $setoresTable->get($id);
            $this->assertFalse($entity->deletado);
            $this->assertTrue($entity->ativo);
        }

        // Não pode acessar deletado
        $this->get('/painel/grupo-01/lojas/2/editar');
        $this->assertResponseCode(404);
        // Não pode acessar de outro grupo
        $this->get('/painel/grupo-01/lojas/5/editar');
        $this->assertResponseCode(404);
        // Loja que não existe
        $this->get('/painel/grupo-01/lojas/58740874/editar');
        $this->assertResponseCode(404);
    }
    public function testEditPutData()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $lojasTable = TableRegistry::get('Lojas');
        $data = $lojasTable->get(1)->toArray();

        $data = [
            'nome' => 'Nome unico diferente',
            'cnpj' => '2981218903',
            'cep' => '27556444',
            'endereco' => 'endereço dirente bem diferente',
            'bairro' => 'Bairro bem diferente',
            'cidade_id' => 2,
            'lat' => 79.0,
            'lng' => 79.0,
            'ativo' => false,
            'setores' => ['_ids' => []]
        ];

        $this->put('/painel/grupo-01/lojas/1/editar', $data);
        $this->assertResponseSuccess();

        $entityEditada = $lojasTable->get(1, ['contain' => ['Setores']]);
        
        // Coloco no Data oq ele nao tinha
        $data['id'] = $entityEditada->id;
        $data['grupo_id'] = $entityEditada->grupo_id;
        $data['deletado'] = $entityEditada->deletado;
        $data['criado_por_id'] = $entityEditada->criado_por_id;
        $data['modificado_por_id'] = $entityEditada->modificado_por_id;
        $data['criado_em'] = $entityEditada->criado_em;
        $data['modificado_em'] = $entityEditada->modificado_em;

        $this->assertEquals(0, count($entityEditada->setores));

        unset($data['setores']);
        unset($entityEditada->setores);
        $this->assertEquals($data, $entityEditada->toArray());
        
    }

    public function testDeleteUnauthenticated()
    {
        $this->session(['Auth' => null]);
        $this->get('/painel/grupo-01/lojas/1/deletar');
        $urlRedirect = array_merge(['?' => ['redirect' => '/painel/grupo-01/lojas/1/deletar']], DATA::LOGIN_ROUTE);
        $this->assertRedirect($urlRedirect);
    }
    public function testDelete()
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        // Inexistente
        $this->delete('/painel/grupo-01/lojas/1321312/deletar.json');
        $this->assertResponseCode(404);
        // De outro grupo
        $this->delete('/painel/grupo-01/lojas/5/deletar.json');
        $this->assertResponseCode(404);
        // Não pode acessar o que ja está deletado
        $this->delete('/painel/grupo-01/lojas/2/deletar.json');
        $this->assertResponseCode(404);

        $this->delete('/painel/grupo-01/lojas/1/deletar.json');
        $this->assertResponseOk();
        $lojas = TableRegistry::get('Lojas');
        $result = $lojas->get(1);
        $this->assertTrue($result->deletado);
    }

}
