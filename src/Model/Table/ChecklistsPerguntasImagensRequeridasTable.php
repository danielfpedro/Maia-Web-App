<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChecklistsPerguntasImagensRequeridas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ChecklistPerguntas
 *
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagensRequerida findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasImagensRequeridasTable extends Table
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

        $this->setTable('checklists_perguntas_imagens_requeridas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ChecklistPerguntas', [
            'foreignKey' => 'checklist_pergunta_id',
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
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

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
        $rules->add($rules->existsIn(['checklist_pergunta_id'], 'ChecklistPerguntas'));

        return $rules;
    }
}
