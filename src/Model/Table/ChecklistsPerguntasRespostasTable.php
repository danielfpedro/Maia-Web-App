<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChecklistsPerguntasRespostas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ChecklistsPerguntas
 * @property \Cake\ORM\Association\BelongsTo $ChecklistsPerguntasAlternativas
 * @property \Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\ChecklistsPerguntasResposta get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasResposta findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasRespostasTable extends Table
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
                      'criado_em' => 'new'
                  ],
              ]
          ]);

        $this->setTable('checklists_perguntas_respostas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Perguntas', [
            'className' => 'ChecklistsPerguntas',
            'foreignKey' => 'checklists_pergunta_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Visitas', [
            'foreignKey' => 'visita_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('AlternativaSelecionada', [
            'className' => 'ChecklistsPerguntasAlternativas',
            'foreignKey' => 'checklists_perguntas_alternativa_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('AlternativasCriticas', [
            'className' => 'ChecklistsPerguntasAlternativas',
            'foreignKey' => 'checklists_perguntas_alternativa_id',
            'joinType' => 'INNER',
            'conditions' => ['AlternativasCriticas.item_critico' => 1]
        ]);

        $this->hasMany('FotosRequeridas', [
            'className' => 'ChecklistsPerguntasFotosRequeridas',
            'foreignKey' => 'checklists_perguntas_resposta_id',
            'saveStrategy' => 'replace'
        ]);

        $this->hasOne('PlanosTaticos', [
            'foreignKey' => 'checklists_perguntas_resposta_id',
            'propertyName' => 'plano_tatico'
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
            ->allowEmpty('resposta_em_texto');

        $validator
            ->allowEmpty('observacao');

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
        $rules->add(function($entity) {
            $visita = $this->Visitas->get($entity->visita_id, ['contain' => ['Checklists.Perguntas.Alternativas']]);
            //Visita já é validada então podemos confiar no grupo_id dela
            $pergunta = $this->Perguntas->get($entity->checklists_pergunta_id, ['contain' => ['Alternativas']]);

            //Id da Checklist deve ser o mesmo da Visita
            if ($pergunta->checklist_id != $visita->checklist_id) {
                return false;
            }

            // Se for resposta em texto não vai ter id da alternativa mesmo
            if ($entity->checklists_perguntas_alternativa_id) {
                $achou = false;
                foreach ($pergunta->alternativas as $alternativa) {
                    if ($alternativa->id == $entity->checklists_perguntas_alternativa_id) {
                        $achou = true;
                    }
                }

                if (!$achou) {
                    return false;
                }
            }
            // Vejo se o id da pergunta que está ligada é do meu grupo que tb
            // foi inserido para validarmos
            return true;
        }, [
            'errorField' => 'checklists_pergunta_id',
            'message' => 'Id da Pergunta inválido.'
        ]);

        return $rules;
    }
}
