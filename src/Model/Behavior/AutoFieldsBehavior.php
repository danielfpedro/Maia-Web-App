<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * InsereCompetencias behavior
 */
class AutoFieldsBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
    	if (array_key_exists('entity', $options)) {

    		if (array_key_exists('userData', $options)) {

				$temId = true;
				if (!array_key_exists('id', $options['userData'])) {
					$temId = false;
				}

				$temGrupoId = true;
				if (!array_key_exists('grupo_id', $options['userData'])) {
					$temGrupoId = false;
				}

    			if ($options['entity']->isNew()) {

					$options['entity']->accessible('criado_por_id', true);
					$options['entity']->accessible('grupo_id', true);
					$options['entity']->accessible('deletado', true);
                    // Para eu testar as regras de validação de deletado
                    // eu aceito nas options setar ele vazio, mais para efeitos de teste
					$data['deletado'] = (array_key_exists('deletado', $options)) ? $options['deletado'] : false;

					if ($temId) {
						$data['criado_por_id'] = $options['userData']['id'];
					}
					if ($temGrupoId) {
						$data['grupo_id'] = $options['userData']['grupo_id'];
					}
    			}
    			
    			$options['entity']->accessible('modificado_por_id', true);
    			if ($temId) {
    				$data['modificado_por_id'] = $options['userData']['id'];
    			}
    		}
    	}
    }

}
