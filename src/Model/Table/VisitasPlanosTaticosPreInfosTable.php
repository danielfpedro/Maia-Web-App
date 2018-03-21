<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\I18n\Time;

use App\Model\Rule\FkBelongsToGrupoRule;

/**
 * VisitasPlanosTaticosPreInfos Model
 *
 * @property \App\Model\Table\VisitasTable|\Cake\ORM\Association\BelongsTo $Visitas
 * @property |\Cake\ORM\Association\BelongsTo $Whos
 * @property \App\Model\Table\SolicitantesTable|\Cake\ORM\Association\BelongsTo $Solicitantes
 *
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\VisitasPlanosTaticosPreInfo findOrCreate($search, callable $callback = null, $options = [])
 */
class VisitasPlanosTaticosPreInfosTable extends Table
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

        $this->setTable('visitas_planos_taticos_pre_infos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                ],
            ]
        ]);     

        $this->belongsTo('Visitas', [
            'foreignKey' => 'visita_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Whos', [
            'className' => 'Usuarios',
            'foreignKey' => 'who_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Solicitantes', [
            'className' => 'Usuarios',
            'foreignKey' => 'solicitante_id',
            'joinType' => 'INNER'
        ]);
    }

    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
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
            ->integer('visita_id')
            ->notEmpty('visita_id');

        $validator
            ->integer('solicitante_id')
            ->notEmpty('solicitante_id');

        $validator
            ->integer('who_id')
            ->notEmpty('who_id');

        $validator
            ->integer('prazo_dias')
            ->requirePresence('prazo_dias', 'create')
            ->notEmpty('prazo_dias');

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
        $rules->add(new FkBelongsToGrupoRule($this->Visitas, 'visita_id'), [
            'errorField' => 'visita_id',
            'message' => 'Visita não pertence ao grupo'
        ]);
        $rules->add(new FkBelongsToGrupoRule($this->Whos, 'solicitante_id'), [
            'errorField' => 'solicitante_id',
            'message' => 'Solicitante não pertence ao grupo'
        ]);
        $rules->add(new FkBelongsToGrupoRule($this->Whos, 'who_id'), [
            'errorField' => 'who_id',
            'message' => 'Executante não pertence ao grupo'
        ]);

        return $rules;
    }
}
