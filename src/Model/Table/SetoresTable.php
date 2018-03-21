<?php
namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

// My Rules
use App\Model\Rule\FkBelongsToGrupoRule;
use App\Model\Rule\LinkedOneOrManyBelongsToGrupoRule;
use App\Model\Rule\RunValidationAgainRule;
use App\Model\Rule\UniqueOnGrupoRule;

// Behaviors
use App\Behavior\SharedBehavior;
use App\Behavior\SaveLogBehavior;
use App\Behavior\AutoFieldsBehavior;

/**
 * Setores Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Grupos
 *
 * @method \App\Model\Entity\Setore get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setore newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Setore[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setore[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setore findOrCreate($search, callable $callback = null, $options = [])
 */
class SetoresTable extends Table
{
    public $moduloId = 3;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // Inflector(em ingles) não singulariou corretamente o Setores para setor então tive que informar aqui
        $this->setEntityClass('App\Model\Entity\Setor');

        $this->setTable('setores');
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

        $this->addBehavior('Shared', ['alias' => $this->alias()]);
        $this->addBehavior('SaveLog', ['moduloId' => $this->moduloId]);
        $this->addBehavior('AutoFields', ['moduloId' => $this->moduloId]);

        // Relacionamentos
        // 
        // Belongs To
        $this->belongsTo('CriadoPor', [
            'className' => 'Usuarios',
            'foreignKey' => 'criado_por_id'
        ]);
        $this->belongsTo('ModificadoPor', [
            'className' => 'Usuarios',
            'foreignKey' => 'modificado_por_id'
        ]);

        // Has Many
        $this->hasMany('Perguntas', [
            'className' => 'ChecklistsPerguntas',
            'foreignKey' => 'setor_id',
        ]);
        $this->belongsToMany('Lojas', [
            'joinTable' => 'lojas_setores',
            'foreignKey' => 'setor_id',
            'targetForeignKey' => 'loja_id',
            'joinType' => 'INNER'
        ]);
    }

    public function todosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type)
            ->where([
                $this->alias() . '.grupo_id' => $usuario['grupo_id'],
            ]);
    }
    public function todosAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosDoMeuGrupo($type, $usuario)
            ->where([
                $this->alias() . '.ativo' => true,
            ]);
    }

    public function todosVivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosDoMeuGrupo($type, $usuario)
            ->where([
                $this->alias() . '.deletado' => false
            ]);
    }

    public function todosVivosEAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosVivosDoMeuGrupo($type, $usuario)
            ->where([
                $this->alias() . '.ativo' => true,
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
            ->maxLength('nome', 200)
            ->notEmpty('nome');

        $validator
            ->requirePresence('ativo', 'create')
            ->boolean('ativo')
            ->notEmpty('ativo');

        // Inseridos manualmente
        // 
        $validator
            ->requirePresence('deletado', 'create')
            ->notEmpty('deletado')
            ->boolean('deletado');

        $validator
            ->requirePresence('criado_por_id', 'create')
            ->notEmpty('criado_por_id')
            ->integer('criado_por_id');

        $validator
            ->requirePresence('modificado_por_id')
            ->notEmpty('modificado_por_id')
            ->integer('modificado_por_id');

        $validator
            ->requirePresence('grupo_id', 'create')
            ->notEmpty('grupo_id')
            ->integer('grupo_id');

        // Só são revalidados no app rules
        $validator
            ->notEmpty('criado_em')
            ->datetime('criado_em');
        $validator
            ->notEmpty('modificado_em')
            ->datetime('modificado_em');

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
        $rules->add(new FkBelongsToGrupoRule($this->CriadoPor, 'criado_por_id'), 'fkBelongsToGrupo', [
            'errorField' => 'criado_por_id',
            'message' => 'Não pertence ao grupo.'
        ]);
        $rules->add(new FkBelongsToGrupoRule($this->ModificadoPor, 'modificado_por_id'), 'fkBelongsToGrupo', [
            'errorField' => 'modificado_por_id',
            'message' => 'Não pertence ao grupo.'
        ]);

        // Deve ser único no grupo
        // 
        $rules->add(new UniqueOnGrupoRule($this, 'nome'), 'uniqueOnGrupo', [
            'errorField' => 'nome',
            'message' => 'Já existe outro Setor cadastrando com este nome.'
        ]);

        // Lojas atracados devem pertencer ao meu grupo
        $rules->add(new LinkedOneOrManyBelongsToGrupoRule('lojas'), 'belongsToGrupo', [
            'errorField' => 'lojas',
            'message' => 'Uma ou mais Lojas selecionadas são inválidas.'
        ]);

        return $rules;
    }
}
