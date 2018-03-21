<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlanosTaticosLogs Model
 *
 * @property \App\Model\Table\PlanosTaticosTable|\Cake\ORM\Association\BelongsTo $PlanosTaticos
 * @property \App\Model\Table\PlanosTaticosLogsTiposTable|\Cake\ORM\Association\BelongsTo $PlanosTaticosLogsTipos
 *
 * @method \App\Model\Entity\PlanosTaticosLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLog findOrCreate($search, callable $callback = null, $options = [])
 */
class PlanosTaticosLogsTable extends Table
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

        $this->setTable('planos_taticos_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Behaviors
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new'
                ],
            ]
        ]);

        // Relationship
        $this->belongsTo('PlanosTaticos', [
            'foreignKey' => 'planos_tatico_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('PlanosTaticosLogsTipos', [
            'foreignKey' => 'planos_taticos_logs_tipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
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
        $rules->add($rules->existsIn(['planos_taticos_logs_tipo_id'], 'PlanosTaticosLogsTipos'));

        return $rules;
    }
}
