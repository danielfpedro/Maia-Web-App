<?php

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;

class UniqueOnGrupo
{
    public function __invoke(EntityInterface $entity, array $options)
    {
        return false;
    }
}

