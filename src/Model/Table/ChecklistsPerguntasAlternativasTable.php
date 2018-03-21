<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChecklistsPerguntasAlternativas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ChecklistsPerguntas
 *
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasAlternativa findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasAlternativasTable extends Table
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

        $this->setTable('checklists_perguntas_alternativas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Perguntas', [
            'className' => 'ChecklistsPerguntas',
            'foreignKey' => 'checklists_pergunta_id',
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
        $rules->add($rules->existsIn(['checklists_pergunta_id'], 'Perguntas'));

        return $rules;
    }
}
