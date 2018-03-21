<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;

class HelperValidationTable
{

    private $_context;
    private $_table;

	public function __construct($context, $tableName)
	{
        $this->_context = $context;
        $this->_table = TableRegistry::get($tableName);
	}

	public function requirePresenceOnlyCreate($fieldName)
	{
        // Ausência no add
        $data = $this->_table->get(1)->toArray();
        unset($data[$fieldName]);

        $newEntity = $this->_table->newEntity();
        $newEntity = $this->_table->patchEntity($newEntity, $data);
        $this->_context->assertEquals(true, isset($newEntity->errors($fieldName)['_required']));

        // No edit, deve passar
        $entity = $this->_table->get(1);
        unset($entity->$fieldName);
        
        $this->_context->assertEquals(false, isset($entity->errors($fieldName)['_required']));
	}

    public function requirePresence($fieldName)
    {
        // Ausência no add
        $data = $this->_table->get(1)->toArray();
        unset($data[$fieldName]);

        $newEntity = $this->_table->newEntity();
        $newEntity = $this->_table->patchEntity($newEntity, $data);
        $this->_context->assertEquals(true, isset($newEntity->errors($fieldName)['_required']));

        // Ausencia no edit
        $entity = $this->_table->get(1);
        unset($entity->$fieldName);
        
        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['_required']));
    }

    public function notEmpty($fieldName)
    {
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = '';

        $newEntity = $this->_table->newEntity();
        $newEntity = $this->_table->patchEntity($newEntity, $data);
        
        $this->_context->assertEquals(true, isset($newEntity->errors($fieldName)['_empty']));

        // Null
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = null;

        $newEntity = $this->_table->newEntity();
        $newEntity = $this->_table->patchEntity($newEntity, $data);

        $this->_context->assertEquals(true, isset($newEntity->errors($fieldName)['_empty']));
    }

    public function maxLength($fieldName, $max)
    {
        $maxPlusOne = ($max + 1);
        $data = $this->_table->get(1)->toArray();

        $data[$fieldName] = '';
        for ($i=0; $i < $maxPlusOne; $i++) { 
            $data[$fieldName] .= 'a';
        }

        $this->_context->assertEquals($maxPlusOne, strlen($data[$fieldName]));

        $newEntity = $this->_table->newEntity();
        $newEntity = $this->_table->patchEntity($newEntity, $data);

        $this->_context->assertEquals(true, isset($newEntity->errors($fieldName)['maxLength']));
    }

    public function uniqueOnGrupo($fieldName, $dataOutroGrupo)
    {
        $data = $this->_table->get(1)->toArray();

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data, ['entity' => $entity, 'userData' => ['id' => 1, 'grupo_id' => 1]]);
        $this->_table->save($entity);
        $this->_context->assertTrue(isset($entity->errors($fieldName)['uniqueOnGrupo']));

        $dataNome01 = $this->_table->get(1)->toArray()['nome'];
        $data02 = $this->_table->get(2);
        $data02->set($fieldName, $dataNome01);

        $this->_table->save($data02);
        $this->_context->assertTrue(isset($entity->errors($fieldName)['uniqueOnGrupo']));

        // Adicionando com nome de outro grupo, deve passar
        // 
        $data = $dataOutroGrupo;

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data, ['entity' => $entity, 'userData' => ['id' => 1, 'grupo_id' => 1]]);
        $this->_table->save($entity);
        
        $this->_context->assertFalse(isset($entity->errors($fieldName)['uniqueOnGrupo']));
        //Deleto para nao bagunçar o test abaixo
        $this->_table->delete($entity);

        // Editando para nome de outro grupo
        // 
        $entity = $this->_table->get(1);
        $entity = $this->_table->patchEntity($entity, [], ['entity' => $entity, 'userData' => ['id' => 1, 'grupo_id' => 1]]);
        $entity->set($fieldName, $dataOutroGrupo[$fieldName]);
        $this->_table->save($entity);
        $this->_context->assertFalse(isset($entity->errors($fieldName)['uniqueOnGrupo']));
    }

    public function boolean($fieldName)
    {
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = 'olá';

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data);

        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['boolean']));

        // Formato inválido integer
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = 2;

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data);

        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['boolean']));
    }

    public function integer($fieldName)
    {
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = 'olá';

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data);
        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['integer']));

        // Formato inválido float
        $data = $this->_table->get(1)->toArray();
        $data[$fieldName] = 1.1;

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data);

        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['integer']));
    }

    public function fkBelongsToGrupo($fieldName)
    {
        $data = $this->_table->get(1)->toArray();

        $entity = $this->_table->newEntity();
        $entity = $this->_table->patchEntity($entity, $data, ['entity' => $entity, 'userData' => ['id' => 1, 'grupo_id' => 2]]);

        $this->_table->save($entity);
        $this->_context->assertEquals(true, isset($entity->errors($fieldName)['fkBelongsToGrupo']));
    }
}