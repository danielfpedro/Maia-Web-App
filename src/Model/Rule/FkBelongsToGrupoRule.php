<?php

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;

class FkBelongsToGrupoRule
{
	private $_table;
    private $_fkIdPath;

	public function __construct($table, $fkIdPath)
	{
        $this->_table = $table;
		$this->_fkIdPath = $fkIdPath;
	}
    public function __invoke(EntityInterface $entity, array $options)
    {
        // $exploded = explode('.', $this->_fkIdPath);
        // $value = clone $entity;
        // foreach ($exploded as $part) {
        //     $value = $value->get($part);
        // }

        $value = $entity->get($this->_fkIdPath);

        // Se nao tem o valor passo, e a ausencia deve ser validada em outra regra
        if (!$value) {
            return true;
        }

        $result = $this->_table->get($value);
        return ($result->grupo_id == $entity->grupo_id);
    }
}

