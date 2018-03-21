<?php
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\SharedBehavior;
use Cake\TestSuite\TestCase;

use App\Model\Table\SetoresTable;
use Cake\ORM\TableRegistry;

/**
 * App\Model\Behavior\TeaBehavior Test Case
 */
class SharedBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Behavior\TeaBehavior
     */
    public $Shared;
    public $Setores;

    public $meuId = 1;
    public $naoMeuId = 112398012830;

    public $fixtures = ['app.setores'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->Setores = TableRegistry::get('Setores');
        // $this->Shared = new SharedBehavior(TableRegistry::get('Setores'));
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Shared);
        unset($this->Setores);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testDoMeuGrupo()
    {
        $query = $this->Setores->find('doMeuGrupo', ['grupo_id' => $this->meuId]);

        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        // Deve haver mais de um resultado, se eu não testar aqui ele não
        // cai no foreach e respectivamente não ativa os assert de dentro do 
        // foreach
        $this->assertGreaterThan(0, count($result));

        foreach ($result as $key => $row) {
            $this->assertEquals($row['grupo_id'], $this->meuId);
        }
    }
    public function testVivos()
    {
        $query = $this->Setores->find('vivos');

        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        // Deve haver mais de um resultado, se eu não testar aqui ele não
        // cai no foreach e respectivamente não ativa os assert de dentro do 
        // foreach
        $this->assertGreaterThan(0, count($result));

        foreach ($result as $key => $row) {
            $this->assertEquals($row['deletado'], false);
        }
    }
    public function testAtivos()
    {
        $query = $this->Setores->find('ativos');

        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        // Deve haver mais de um resultado, se eu não testar aqui ele não
        // cai no foreach e respectivamente não ativa os assert de dentro do 
        // foreach
        $this->assertGreaterThan(0, count($result));

        foreach ($result as $key => $row) {
            $this->assertEquals($row['ativo'], true);
        }
    }

    public function testDoMeuGrupoAtivosEVivos()
    {
        $query = $this->Setores->find('doMeuGrupo', ['grupo_id' => $this->meuId])
            ->find('vivos')
            ->find('ativos');

        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        // Deve haver mais de um resultado, se eu não testar aqui ele não
        // cai no foreach e respectivamente não ativa os assert de dentro do 
        // foreach
        $this->assertGreaterThan(0, count($result));

        foreach ($result as $key => $row) {
            $this->assertEquals($row['grupo_id'], $this->meuId);
            $this->assertEquals($row['deletado'], false);
            $this->assertEquals($row['ativo'], true);
        }
    }

}
