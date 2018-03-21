<?php

namespace Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\I18n\Time;

/**
* 
*/
class Person
{
	
	private $name;

	/**
	 * @param string Nome que o boneco vai receber.
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}
}

$person = new Person();
$person->setName();

?>