<?php

namespace App\Test\TestCase\Model\Entity;

use App\Model\Table\SetoresTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SetorTest extends TestCase
{
	public $fixtures = ['app.setores'];

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

    public function testMassAssignProtection()
    {
        $cleanData = [
            'nome' => '90dsaiopkdsa8d90saiojsa8dsauoidsad980saio',
            'ativo' => true
        ];
        $dirtyData = [
            'id'=> 10,
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'deletado' => false,
            'criado_em' => '2011-10-01',
            'modificado_em' => '2011-10-01',
        ];

        $data = array_merge($cleanData, $dirtyData);
        
        $newEntity = $this->Setores->newEntity();
        $newEntity = $this->Setores->patchEntity($newEntity, $data);
        
        $this->assertEquals($newEntity->toArray(), $cleanData);
    }

}