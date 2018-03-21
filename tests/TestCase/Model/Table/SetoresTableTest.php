<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SetoresTable;
use App\Test\TestCase\Model\Table\HelperValidationTable;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

use App\Test\TestCase\Painel\Data;

class SetoresTableTest extends TestCase
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
        'app.lojas_setores'
    ];

	public function setUp()
    {
        parent::setUp();

        $config = TableRegistry::exists('Setores') ? [] : ['className' => SetoresTable::class];
        $this->Setores = TableRegistry::get('Setores', $config);
    }

    public function tearDown()
    {
    	unset($this->Setores);
    	TableRegistry::clear();
    }

    public function testValidationDefault()
    {
        $data = [
            'nome' => '',
            'grupo_id' => '',
            'ativo' => '',
            'deletado' => '',
            'criado_por_id' => '',
            'modificado_por_id' => '',
        ];

        // Require presence
        // 
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, []);
        $this->Setores->save($setor);

        $this->assertTrue(isset($setor->errors('nome')['_required']));
        $this->assertTrue(isset($setor->errors('grupo_id')['_required']));
        $this->assertTrue(isset($setor->errors('ativo')['_required']));
        $this->assertTrue(isset($setor->errors('deletado')['_required']));
        $this->assertTrue(isset($setor->errors('criado_por_id')['_required']));
        $this->assertTrue(isset($setor->errors('modificado_por_id')['_required']));

        // notEmpty
        // 
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'deletado' => '', 'userData' => ['id' => '', 'grupo_id' => '']]);
        $this->Setores->save($setor);

        $this->assertTrue(isset($setor->errors('nome')['_empty']));
        $this->assertTrue(isset($setor->errors('grupo_id')['_empty']));
        $this->assertTrue(isset($setor->errors('ativo')['_empty']));
        $this->assertTrue(isset($setor->errors('deletado')['_empty']));
        $this->assertTrue(isset($setor->errors('criado_por_id')['_empty']));
        $this->assertTrue(isset($setor->errors('modificado_por_id')['_empty']));

        //Integer
        //
        $data['grupo_id'] = 'a';
        $data['criado_por_id'] = 'a';
        $data['modificado_por_id'] = 'a';

        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data);
        $this->Setores->save($setor);
        
        $this->assertTrue(isset($setor->errors('grupo_id')['integer']));
        $this->assertTrue(isset($setor->errors('criado_por_id')['integer']));
        $this->assertTrue(isset($setor->errors('modificado_por_id')['integer']));

        // Boolean
        $data['deletado'] = 'a';
        $data['ativo'] = 'a';

        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data);
        $this->Setores->save($setor);
        
        $this->assertTrue(isset($setor->errors('deletado')['boolean']));
        $this->assertTrue(isset($setor->errors('ativo')['boolean']));
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
        $data = $this->Setores->get(1)->toArray();
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);
        $this->Setores->save($setor);

        $this->assertTrue(isset($setor->errors('nome')['uniqueOnGrupo']));

        // Mesmo nome de outro grupo, deve passar
        $data = $this->Setores->get(1)->toArray();
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $setor->set('nome', $this->Setores->get(5)->toArray()['nome']);
        $this->Setores->save($setor);

        $this->assertFalse(isset($setor->errors('nome')['uniqueOnGrupo']));

        // Mesmo nome de um deletado, deve passar
        $data = $this->Setores->get(1)->toArray();
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $setor->set('nome', $this->Setores->get(2)->toArray()['nome']);
        $this->Setores->save($setor);

        $this->assertFalse(isset($setor->errors('nome')['uniqueOnGrupo']));

        // Criado por id
        // 
        // Deve pertecener ao grupo
        $data = $this->Setores->get(1)->toArray();
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 5, 'grupo_id' => 1]]);

        $setor->set('nome', 'nome unico que nao existe');

        $this->Setores->save($setor);
        $this->assertTrue(isset($setor->errors('criado_por_id')['fkBelongsToGrupo']));

        // Modificado por id
        // 
        // Deve pertecener ao grupo
        $data = $this->Setores->get(1)->toArray();
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 5, 'grupo_id' => 1]]);

        $setor->set('nome', 'nome unico que nao existe');

        $this->Setores->save($setor);
        $this->assertTrue(isset($setor->errors('modificado_por_id')['fkBelongsToGrupo']));

        // Linked belongs to grupo
        // 
        // De outro grupo
        $data = $this->Setores->get(1)->toArray();
        $data['lojas']['_ids'] = [5];
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $setor->set('nome', 'nome unico que nao existe');

        $this->Setores->save($setor);
        
        $this->assertTrue(isset($setor->errors('lojas')['belongsToGrupo']));

        // Do mesmo grupo mas deletado
        $data = $this->Setores->get(1)->toArray();
        $data['lojas']['_ids'] = [2];
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $setor->set('nome', 'nome unico que nao existe');

        $this->Setores->save($setor);

        $this->assertTrue(isset($setor->errors('lojas')['belongsToGrupo']));

        // Somente do grupo, deve passar
        $data = $this->Setores->get(1)->toArray();
        $data['lojas']['_ids'] = [1,3];
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $setor->set('nome', 'nome unico que nao existe');

        $this->Setores->save($setor);
        
        $this->assertFalse(isset($setor->errors('lojas')['belongsToGrupo']));

    }

    public function testAutoFields()
    {
        $data = $this->Setores->get(1)->toArray();
        
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $this->assertEquals(1, $setor->criado_por_id);
        $this->assertEquals(1, $setor->modificado_por_id);
        $this->assertEquals(1, $setor->grupo_id);
        $this->assertEquals(false, $setor->deletado);
        
        // No edit nao pode mudar criado por e deve mudar modificado por, grupo_id tb nao pode ser modificado
        $setor = $this->Setores->get(1);
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 2, 'grupo_id' => 500]]);

        $this->assertEquals(1, $setor->criado_por_id);
        $this->assertEquals(2, $setor->modificado_por_id);
        $this->assertEquals(1, $setor->grupo_id);
        $this->assertEquals(false, $setor->deletado);

    }

    public function testAfterSave()
    {
        // No add
        // 
        $data = $this->Setores->get(1)->toArray();
        $data['nome'] = 'Nome unico';
        
        $setor = $this->Setores->newEntity();
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1, 'grupo_id' => 1]]);

        $saveResult = $this->Setores->save($setor);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 3 é setores
        $this->assertEquals(3, $log->modulo_id);
        // 3 é id de adição
        $this->assertEquals(3, $log->logs_tipo_id);

        // No edit
        //
        $setor = $this->Setores->get(1);
        $data = ['nome' => 'novo nome unico'];
        $setor = $this->Setores->patchEntity($setor, $data, ['entity' => $setor, 'userData' => ['id' => 1]]);
        $saveResult = $this->Setores->save($setor);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 3 é setores
        $this->assertEquals(3, $log->modulo_id);
        // 3 é id de edição
        $this->assertEquals(2, $log->logs_tipo_id);

        // No delete
        //
        $setor = $this->Setores->get(1);
        $setor->set('deletado', true);
        $setor = $this->Setores->save($setor, [], ['entity' => $setor, 'userData' => ['id' => 1]]);
        $saveResult = $this->Setores->save($setor);
        $this->assertNotEquals(false, $saveResult);

        $logs = TableRegistry::get('Logs');
        $log = $logs->find()->last();
        // Modulo 3 é setores
        $this->assertEquals(3, $log->modulo_id);
        // 3 é id de edição
        $this->assertEquals(1, $log->logs_tipo_id);
    }

}