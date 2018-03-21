<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Collection\Collection;

/**
 * ChecklistsPerguntasSetoresOrdem Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Checklists
 * @property \Cake\ORM\Association\BelongsTo $Setors
 *
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasSetoresOrdem findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasSetoresOrdemTable extends Table
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

        $this->setTable('checklists_perguntas_setores_ordem');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Checklists', [
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Setores', [
            'foreignKey' => 'setor_id',
            'propertyName' => 'setor',
            'joinType' => 'INNER'
        ]);

    }

    public function deletaSemPerguntas($checklistId)
    {
        $checklist = $this->Checklists->get($checklistId, ['contain' => ['OrdemSetores', 'Perguntas']]);
        $checklistPerguntasSetoresIds = array_unique((new Collection($checklist->perguntas))->extract('setor_id')->toArray());

        foreach ($checklist->ordem_setores as $ordem) {
            if (!in_array($ordem->setor_id, $checklistPerguntasSetoresIds)) {
                $ordemSetorDeletar = $this->get($ordem->id);
                $this->deleteOrFail($ordemSetorDeletar);
            }
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
            ->integer('ordem')
            ->requirePresence('ordem', 'create')
            ->notEmpty('ordem');

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
        $rules->add($rules->existsIn(['checklist_id'], 'Checklists'));
        $rules->add($rules->existsIn(['setor_id'], 'Setores'));

        return $rules;
    }
}
