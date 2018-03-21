<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlanosTaticosTarefas Model
 *
 * @property \App\Model\Table\PlanosTaticosTable|\Cake\ORM\Association\BelongsTo $PlanosTaticos
 *
 * @method \App\Model\Entity\PlanosTaticosTarefa get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosTarefa findOrCreate($search, callable $callback = null, $options = [])
 */
class PlanosTaticosTarefasTable extends Table
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

        $this->setTable('planos_taticos_tarefas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                ],
            ]
        ]);

        $this->belongsTo('PlanosTaticos', [
            'foreignKey' => 'planos_tatico_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('QuemCriou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_id',
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
            ->requirePresence('descricao', 'create')
            ->notEmpty('descricao');

        $validator
            ->allowEmpty('responsavel');
        $validator
            ->allowEmpty('how_much');

        $validator
            ->date('prazo')
            ->allowEmpty('prazo');

        // $validator
        //     ->requirePresence('prazo_placeholder', 'create')
        //     ->notEmpty('prazo_placeholder');

        $validator
            ->date('dt_conclusao');
        $validator
            ->date('dt_cancelamento');

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
        $rules->add($rules->existsIn(['planos_tatico_id'], 'PlanosTaticos'));

        return $rules;
    }
}
