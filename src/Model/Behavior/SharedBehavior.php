<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * Shared behavior
 */
class SharedBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function findMeus(Query $query, array $options)
    {
        if (!array_key_exists('id', $options)) {
            throw new \Exception("Você deve informar o id do user logado");
        }
        
        return $query->where([$this->_config['alias'] . '.user_id' => (int)$options['id']]);
    }

    public function findVivos(Query $query, array $options)
    {
        return $query->where([$this->_config['alias'] . '.is_alive' => true]);
    }

    public function findAtivos(Query $query, array $options)
    {
        return $query->where([$this->_config['alias'] . '.is_active' => true]);
    }
}
