<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bairros Model
 *
 * @property \Cake\ORM\Association\HasMany $Enderecos
 *
 * @method \App\Model\Entity\Bairro get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bairro newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Bairro[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bairro|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bairro patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bairro[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bairro findOrCreate($search, callable $callback = null, $options = [])
 */
class BairrosTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('bairros');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Enderecos', [
            'foreignKey' => 'bairro_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        return $validator;
    }
}
