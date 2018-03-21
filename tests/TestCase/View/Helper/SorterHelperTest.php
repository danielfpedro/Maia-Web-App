<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\SorterHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\SorterHelper Test Case
 */
class SorterHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\View\Helper\SorterHelper
     */
    public $Sorter;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Sorter = new SorterHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Sorter);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
