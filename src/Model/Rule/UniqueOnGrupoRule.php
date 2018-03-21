<?php

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;

class UniqueOnGrupoRule
{
	private $_tableContext;
	private $_fieldName;

	public function __construct($tableContext, $fieldName)
	{
		$this->_tableContext = $tableContext;
		$this->_fieldName = $fieldName;
	}
    public function __invoke(EntityInterface $entity, array $options)
    {
    	$conditions = [
    		$this->_fieldName => $entity->get($this->_fieldName),
    		'grupo_id' => $entity->grupo_id,
    		'deletado' => false
    	];

        if (!$entity->isNew()) {
        	$conditions[$this->_fieldName . ' !='] = $entity->getOriginal($this->_fieldName);
        }

        return ($this->_tableContext->find()->where($conditions)->count() < 1);
    }
}

