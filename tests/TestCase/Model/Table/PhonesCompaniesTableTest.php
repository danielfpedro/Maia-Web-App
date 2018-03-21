<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PhonesCompaniesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PhonesCompaniesTable Test Case
 */
class PhonesCompaniesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PhonesCompaniesTable
     */
    public $PhonesCompanies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.phones_companies'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PhonesCompanies') ? [] : ['className' => PhonesCompaniesTable::class];
        $this->PhonesCompanies = TableRegistry::get('PhonesCompanies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PhonesCompanies);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
