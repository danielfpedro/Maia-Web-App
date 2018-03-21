<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Network\Session;
use Cake\Event\Event;
use ArrayObject;

/**
 * ModelosAlternativasAlternativas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ModelosAlternativas
 *
 * @method \App\Model\Entity\ModelosAlternativasAlternativa get($primaryKey, $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativasAlternativa findOrCreate($search, callable $callback = null, $options = [])
 */
class ModelosAlternativasAlternativasTable extends Table
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

        $this->setTable('modelos_alternativas_alternativas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ModelosAlternativas', [
            'foreignKey' => 'modelos_alternativa_id',
            'joinType' => 'INNER'
        ]);
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (!isset($data['tem_foto'])) {
            $data['tem_foto'] = 0;
        } else {
            $data['tem_foto'] = 1;
        }
        if (!isset($data['item_critico'])) {
            $data['item_critico'] = 0;
        } else {
            $data['item_critico'] = 1;
        }
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
            ->requirePresence('alternativa', 'create')
            ->notEmpty('alternativa');

        $validator
            ->integer('valor')
            ->allowEmpty('valor');

        $validator
            ->requirePresence('tem_foto', 'create')
            ->notEmpty('tem_foto');

        $validator
            ->requirePresence('item_critico', 'create')
            ->notEmpty('item_critico');

        $validator
            ->integer('ordem')
            ->notEmpty('ordem', 'create');

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
        $rules->add($rules->existsIn(['modelos_alternativa_id'], 'ModelosAlternativas'));

        return $rules;
    }
}
