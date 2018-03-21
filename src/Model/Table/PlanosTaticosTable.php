<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlanosTaticos Model
 *
 * @property \App\Model\Table\ChecklistsPerguntasRespostasTable|\Cake\ORM\Association\BelongsTo $ChecklistsPerguntasRespostas
 *
 * @method \App\Model\Entity\PlanosTatico get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlanosTatico newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlanosTatico[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTatico|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlanosTatico patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTatico[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTatico findOrCreate($search, callable $callback = null, $options = [])
 */
class PlanosTaticosTable extends Table
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

        $this->setTable('planos_taticos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                ],
            ]
        ]);     

        $this->belongsTo('Respostas', [
            'className' => 'ChecklistsPerguntasRespostas',
            'foreignKey' => 'checklists_perguntas_resposta_id',
            'joinType' => 'INNER'
        ]);

        $this->hasMany('Tarefas', [
            'className' => 'PlanosTaticosTarefas'
        ]);

        $this->hasMany('PlanosTaticosLogs');

        $this->belongsTo('QuemCriou', [
            'propertyName' => 'quem_criou',
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_id'
        ]);

        $this->belongsTo('Solicitantes', [
            'propertyName' => 'solicitante',
            'className' => 'Usuarios',
            'foreignKey' => 'solicitante_id'
        ]);


        $this->belongsTo('Whos', [
            'propertyName' => 'who',
            'className' => 'Usuarios',
            'foreignKey' => 'who_id'
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
            ->requirePresence('what', 'create')
            ->notEmpty('what');

        $validator
            ->integer('who_id')
            ->requirePresence('who_id', 'create')
            ->notEmpty('who_id');

        $validator
            ->integer('solicitante_id')
            ->requirePresence('solicitante_id', 'create')
            ->notEmpty('solicitante_id');

        $validator
            ->requirePresence('why', 'create')
            ->notEmpty('why');

        $validator
            ->allowEmpty('who');

        $validator
            ->allowEmpty('how_much');

        $validator
            ->allowEmpty('how');

        $validator
            ->requirePresence('when_start', 'create')
            ->date('when_start')
            ->notEmpty('when_start');

        $validator
            ->requirePresence('when_end', 'create')
            ->date('when_end')
            ->notEmpty('when_end');

        $validator
            ->requirePresence('when_start_placeholder', 'create')
            ->notEmpty('when_start_placeholder');

        $validator
            ->requirePresence('when_end_placeholder', 'create')
            ->notEmpty('when_end_placeholder');

        $validator
            ->integer('andamento');

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
        $rules->add($rules->existsIn(['checklists_perguntas_resposta_id'], 'Respostas'));

        return $rules;
    }
}
