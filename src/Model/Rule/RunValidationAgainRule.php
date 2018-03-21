<?php

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;

class RunValidationAgainRule
{
	private $context;

	public function __construct($context)
	{
		$this->context = $context;
	}
    public function __invoke(EntityInterface $entity, array $options)
    {
        $data = $entity->extract($this->context->schema()->columns(), true);
        $validator = $this->context->validator('default');
        $errors = $validator->errors($data, $entity->isNew());
        $entity->errors($errors);

        return empty($errors);
    }

}

