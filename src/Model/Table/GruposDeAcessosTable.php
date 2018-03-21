<?php
namespace App\Model\Table;

use App\Model\Rule\UniqueOnGrupoRule;
use App\Model\Rule\BelongsToGrupoRule;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GruposDeAcessos Model
 *
 * @property \App\Model\Table\GruposTable|\Cake\ORM\Association\BelongsTo $Grupos
 * @property |\Cake\ORM\Association\BelongsTo $Culpados
 *
 * @method \App\Model\Entity\GruposDeAcesso get($primaryKey, $options = [])
 * @method \App\Model\Entity\GruposDeAcesso newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GruposDeAcesso[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeAcesso|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GruposDeAcesso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeAcesso[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeAcesso findOrCreate($search, callable $callback = null, $options = [])
 */
class GruposDeAcessosTable extends Table
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

        $this->setTable('grupos_de_acessos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        // Behaviors
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                    'modificado_em' => 'existing',
                ],
            ]
        ]);

        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('QuemGravou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_id',
            'joinType' => 'INNER'
        ]);

        // BELONGS TO MANY
        $this->belongsToMany('Checklists');
        $this->belongsToMany('Usuarios');
    }

    public function todosDoMeuGrupo($type = 'all', $user)
    {
        return $this->find($type)
            ->where([
                $this->alias() . '.grupo_id' => (int)$user['grupo_id']
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
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

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
        // Deve ser aqui mesmo pois eu só coloco o grupo depois do patch
        $rules->add(function($entity) {
            return !empty($entity->grupo_id);
        });
        $rules->add(function($entity) {
            return !empty($entity->culpado_id);
        });

        // Deve ser único no grupo
        $rules->add(new UniqueOnGrupoRule($this, 'nome'), 'uniqueOnGrupo', [
            'errorField' => 'nome',
            'message' => 'Já existe outro Grupo de Acesso cadastrando com este nome.'
        ]);

        return $rules;
    }
}
