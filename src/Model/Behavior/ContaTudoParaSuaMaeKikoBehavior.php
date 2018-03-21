<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;

/**
 * ContaTudoParaSuaMaeKiko behavior
 */
class ContaTudoParaSuaMaeKikoBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'new' => 'culpado_novo_id',
        'existing' => 'culpado_modificacao_id'
    ];

    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if ($entity->isNew()) {
            $flag = 'new';
        } else {
            $flag = 'existing';
        }
        if (!$entity->sem_culpado) {
            $entity->set($this->config()[$flag], ($entity->culpado_id) ? $entity->culpado_id : $this->config()['culpado_id']);
        }
        
    }
}
