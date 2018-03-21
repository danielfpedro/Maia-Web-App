<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * SaveLog behavior
 */
class SaveLogBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function afterSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $logs = TableRegistry::get('Logs');
        $result = $logs->customSave($this->_config['moduloId'], $entity);
    }

}
