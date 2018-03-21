<?php

/**
 * Verifico se o "One" ou "Many" que ele está ligando pertence ao meu grupo, e se está vivo 
 */

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;

class LinkedOneOrManyBelongsToGrupoRule
{
	private $_fieldName;

	public function __construct($fieldName)
	{
		$this->_fieldName = $fieldName;
	}
    public function __invoke(EntityInterface $entity, array $options)
    {
        $out = true;
        if ($entity->get($this->_fieldName)) {
            if (is_array($entity->get($this->_fieldName))) {
                foreach ($entity->get($this->_fieldName) as $value) {
                    if (!$this->_isGood($value, $entity)) {
                        return false;
                    }
                }
            } else {
                return $this->_isGood($entity->get($this->_fieldName), $entity);
            }
        }
        return $out;
    }

    private function _isGood($value, $entity) {
        return !($value->grupo_id != $entity->grupo_id || $value->deletado);
    }
}

