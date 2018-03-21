<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Loja;

use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

/**
 * App\Model\Entity\Lojas Test Case
 */
class LojaTest extends TestCase
{
        /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = ['app.lojas'];

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Lojas
     */
    public $Lojas;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Loja = new Loja();
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
     * Test initial setup
     *
     * @return void
     */
    public function testMassAssignProtection()
    {
        $cleanData = [
            'nome' => 'lkjdsajk',
            'cnpj' => 'lkjdsajk',
            'cep' => 'lkjdsajk',
            'endereco' => 'lkjdsajk',
            'bairro' => 'lkjdsajk',
            'cidade_id' => 1,
            'lat' => 1,
            'lng' => 1,
            'ativo' => true,
        ];

        $dirtyData = [
            'id'=> 1,
            'grupo_id' => 1,
            'criado_por_id' => 1,
            'modificado_por_id' => 1,
            'deletado' => false,
            'criado_em' => '2011-10-01',
            'modificado_em' => '2011-10-01',
        ];

        $data = array_merge($cleanData, $dirtyData);
        
        $lojasTable = TableRegistry::get('Lojas');

        $newEntity = $lojasTable->newEntity();
        $newEntity = $lojasTable->patchEntity($newEntity, $data);
        
        $this->assertEquals($newEntity->toArray(), $cleanData);
    }
}
