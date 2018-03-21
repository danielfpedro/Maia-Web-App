<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

class HelperController extends IntegrationTestCase
{
	public function assertNotThisMethods($methods, $url)
	{
		foreach ($methods as $method) {
			$this->$method($url);
			$this->assertResponseCode(405);
		}
	}	
}