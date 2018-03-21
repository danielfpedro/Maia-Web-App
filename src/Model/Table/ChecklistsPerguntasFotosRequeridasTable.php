<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChecklistsPerguntasFotosRequeridas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ChecklistsPerguntas
 *
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasFotosRequerida findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasFotosRequeridasTable extends Table
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

        $this->addBehavior('Timestamp', [
          'events' => [
              'Model.beforeSave' => [
                  'criado_em' => 'new',
              ],
          ]
        ]);

        $this->setTable('checklists_perguntas_fotos_requeridas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ChecklistsPerguntas', [
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
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');


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
        $rules->add($rules->existsIn(['checklists_pergunta_id'], 'ChecklistsPerguntas'));

        return $rules;
    }
}
