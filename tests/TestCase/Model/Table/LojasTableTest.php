<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LojasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LojasTable Test Case
 */
class LojasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\LojasTable
     */
    public $Lojas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.lojas',
        'app.setores',
        'app.usuarios',
        'app.logs',
        'app.logs_tipos',
        'app.modulos',
        'app.grupos',
        'app.cidades',
        'app.lojas_setores',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Lojas') ? [] : ['className' => LojasTable::class];
        $this->Lojas = TableRegistry::get('Lojas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Lojas);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $data = [
            'nome' => '',
            'grupo_id' => '',
            'cep' => '',
            'endereco' => '',
            'bairro' => '',
            'cidade_id' => '',
            'lat' => '',
            'lng' => '',
            'ativo' => '',
            'deletado' => '',
            'criado_por_id' => '',
            'modificado_por_id' => '',
        ];

        // Require presence
        // 
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, []);
        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('nome')['_required']));
        $this->assertTrue(isset($loja->errors('grupo_id')['_required']));
        $this->assertTrue(isset($loja->errors('cep')['_required']));
        $this->assertTrue(isset($loja->errors('endereco')['_required']));
        $this->assertTrue(isset($loja->errors('bairro')['_required']));
        $this->assertTrue(isset($loja->errors('cidade_id')['_required']));
        $this->assertTrue(isset($loja->errors('lat')['_required']));
        $this->assertTrue(isset($loja->errors('lng')['_required']));
        $this->assertTrue(isset($loja->errors('ativo')['_required']));
        $this->assertTrue(isset($loja->errors('deletado')['_required']));
        $this->assertTrue(isset($loja->errors('criado_por_id')['_required']));
        $this->assertTrue(isset($loja->errors('modificado_por_id')['_required']));

        // notEmpty
        // 
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'deletado' => '', 'userData' => ['id' => '', 'grupo_id' => '']]);
        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('nome')['_empty']));
        $this->assertTrue(isset($loja->errors('grupo_id')['_empty']));
        $this->assertTrue(isset($loja->errors('cep')['_empty']));
        $this->assertTrue(isset($loja->errors('endereco')['_empty']));
        $this->assertTrue(isset($loja->errors('bairro')['_empty']));
        $this->assertTrue(isset($loja->errors('cidade_id')['_empty']));
        $this->assertTrue(isset($loja->errors('lat')['_empty']));
        $this->assertTrue(isset($loja->errors('lng')['_empty']));
        $this->assertTrue(isset($loja->errors('ativo')['_empty']));
        $this->assertTrue(isset($loja->errors('deletado')['_empty']));
        $this->assertTrue(isset($loja->errors('criado_por_id')['_empty']));
        $this->assertTrue(isset($loja->errors('modificado_por_id')['_empty']));

        //Integer
        //
        $data['cidade_id'] = 'a';
        $data['grupo_id'] = 'a';
        $data['criado_por_id'] = 'a';
        $data['modificado_por_id'] = 'a';

        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data);
        $this->Lojas->save($loja);
        
        $this->assertTrue(isset($loja->errors('cidade_id')['integer']));
        $this->assertTrue(isset($loja->errors('grupo_id')['integer']));
        $this->assertTrue(isset($loja->errors('criado_por_id')['integer']));
        $this->assertTrue(isset($loja->errors('modificado_por_id')['integer']));

        // Boolean
        $data['deletado'] = 'a';
        $data['ativo'] = 'a';

        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data);
        $this->Lojas->save($loja);
        
        $this->assertTrue(isset($loja->errors('deletado')['boolean']));
        $this->assertTrue(isset($loja->errors('ativo')['boolean']));

        // Latitude
        $data['lat'] = 'a';

        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data);
        $this->Lojas->save($loja);
        
        $this->assertTrue(isset($loja->errors('lat')['latitude']));

        // Longitude
        $data['lng'] = 'a';

        $loja = $this->Lojas->newEntity();
        $lojas = $this->Lojas->patchEntity($loja, $data);
        $this->Lojas->save($loja);
        
        $this->assertTrue(isset($loja->errors('lng')['longitude']));
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        // Nome unico no grupo
        // 
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);
        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('nome')['uniqueOnGrupo']));

        // Mesmo nome de outro grupo, deve passar
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('nome', $this->Lojas->get(5)->toArray()['nome']);
        $this->Lojas->save($loja);

        $this->assertFalse(isset($loja->errors('nome')['uniqueOnGrupo']));

        // Mesmo nome de um deletado, deve passar
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('nome', $this->Lojas->get(2)->toArray()['nome']);
        $this->Lojas->save($loja);

        $this->assertFalse(isset($loja->errors('nome')['uniqueOnGrupo']));

        // Cidade exists In
        // 
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('cidade_id', 5000);
        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('cidade_id')['_existsIn']));

        // Criado por id
        // 
        // Deve pertecener ao grupo
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 5, 'grupo_id' => 1]]);

        $loja->set('nome', 'nome unico que nao existe');

        $this->Lojas->save($loja);
        $this->assertTrue(isset($loja->errors('criado_por_id')['fkBelongsToGrupo']));

        // Modificado por id
        // 
        // Deve pertecener ao grupo
        $data = $this->Lojas->get(1)->toArray();
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 5, 'grupo_id' => 1]]);

        $loja->set('nome', 'nome unico que nao existe');

        $this->Lojas->save($loja);
        $this->assertTrue(isset($loja->errors('modificado_por_id')['fkBelongsToGrupo']));

        // Linked belongs to grupo
        // 
        // De outro grupo
        $data = $this->Lojas->get(1)->toArray();
        $data['setores']['_ids'] = [5];
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('nome', 'nome unico que nao existe');

        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('setores')['belongsToGrupo']));

        // Do mesmo grupo mas deletado
        $data = $this->Lojas->get(1)->toArray();
        $data['setores']['_ids'] = [2];
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('nome', 'nome unico que nao existe');

        $this->Lojas->save($loja);

        $this->assertTrue(isset($loja->errors('setores')['belongsToGrupo']));

        // Somente do grupo, deve passar
        $data = $this->Lojas->get(1)->toArray();
        $data['setores']['_ids'] = [1,3];
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $loja->set('nome', 'nome unico que nao existe');

        $this->Lojas->save($loja);
        
        $this->assertFalse(isset($loja->errors('setores')['belongsToGrupo']));

    }

    public function testAutoFields()
    {
        $data = $this->Lojas->get(1)->toArray();
        
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $this->assertEquals(1, $loja->criado_por_id);
        $this->assertEquals(1, $loja->modificado_por_id);
        $this->assertEquals(1, $loja->grupo_id);
        $this->assertEquals(false, $loja->deletado);
        
        // No edit nao pode mudar criado por e deve mudar modificado por, grupo_id tb nao pode ser modificado
        $loja = $this->Lojas->get(1);
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 2, 'grupo_id' => 500]]);

        $this->assertEquals(1, $loja->criado_por_id);
        $this->assertEquals(2, $loja->modificado_por_id);
        $this->assertEquals(1, $loja->grupo_id);
        $this->assertEquals(false, $loja->deletado);

    }

    public function testLog()
    {
        // No add
        // 
        $data = $this->Lojas->get(1)->toArray();
        $data['nome'] = 'Nome unico';
        
        $loja = $this->Lojas->newEntity();
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $saveResult = $this->Lojas->save($loja);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 4 é setores
        $this->assertEquals(4, $log->modulo_id);
        // 3 é id de adição
        $this->assertEquals(3, $log->logs_tipo_id);

        // No edit
        //
        $loja = $this->Lojas->get(1);
        $data = ['nome' => 'novo nome unico'];
        $loja = $this->Lojas->patchEntity($loja, $data, ['entity' => $loja, 'userData' => ['id' => 1]]);
        $saveResult = $this->Lojas->save($loja);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 4 é setores
        $this->assertEquals(4, $log->modulo_id);
        // 3 é id de edição
        $this->assertEquals(2, $log->logs_tipo_id);

        // No delete
        //
        $loja = $this->Lojas->get(1);
        $loja->set('deletado', true);
        $loja = $this->Lojas->save($loja, [], ['entity' => $loja, 'userData' => ['id' => 1]]);
        $saveResult = $this->Lojas->save($loja);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 4 é setores
        $this->assertEquals(4, $log->modulo_id);
        // 1 é id de deletar
        $this->assertEquals(1, $log->logs_tipo_id);
    }
}
