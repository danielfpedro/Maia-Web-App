<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LojasSetores Model
 *
 * @property \App\Model\Table\setoresTable|\Cake\ORM\Association\BelongsTo $Setors
 * @property \App\Model\Table\LojasTable|\Cake\ORM\Association\BelongsTo $Lojas
 *
 * @method \App\Model\Entity\LojasSetor get($primaryKey, $options = [])
 * @method \App\Model\Entity\LojasSetor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LojasSetor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LojasSetor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LojasSetor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LojasSetor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LojasSetor findOrCreate($search, callable $callback = null, $options = [])
 */
class LojasSetoresTable extends Table
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

        // Não converteu o singular setores então setei na mão
        $this->setEntityClass('App\Model\Entity\LojasSetor');

        // Behaviors
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                ],
            ]
        ]);

        $this->setTable('lojas_setores');
        $this->setDisplayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Setores', [
            'foreignKey' => 'setor_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Lojas', [
            'foreignKey' => 'loja_id',
            'joinType' => 'INNER'
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
            ->dateTime('criado_em')
            ->requirePresence('criado_em', 'create')
            ->notEmpty('criado_em');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['setor_id'], 'Setores'));
        $rules->add($rules->existsIn(['loja_id'], 'Lojas'));

        return $rules;
    }
}
