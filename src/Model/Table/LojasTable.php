<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Validation\Validation;

// Rules
// My Rules
use App\Model\Rule\FkBelongsToGrupoRule;
use App\Model\Rule\LinkedOneOrManyBelongsToGrupoRule;
use App\Model\Rule\UniqueOnGrupoRule;

// Behaviors
use App\Behavior\SharedBehavior;
use App\Behavior\SaveLogBehavior;
use App\Behavior\AutoFieldsBehavior;

/**
 * Lojas Model
 *
 * @method \App\Model\Entity\Loja get($primaryKey, $options = [])
 * @method \App\Model\Entity\Loja newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Loja[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Loja|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Loja patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Loja[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Loja findOrCreate($search, callable $callback = null, $options = [])
 */
class LojasTable extends Table
{
    
    public $moduloId = 4;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('lojas');
        $this->displayField('nome');
        $this->primaryKey('id');

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
        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->belongsTo('Cidades');
        $this->belongsTo('CriadoPor', [
            'className' => 'Usuarios',
            'foreignKey' => 'criado_por_id'
        ]);
        $this->belongsTo('ModificadoPor', [
            'className' => 'Usuarios',
            'foreignKey' => 'modificado_por_id'
        ]);

        $this->belongsToMany('Setores', [
            'joinTable' => 'lojas_setores',
            'foreignKey' => 'loja_id',
            'targetForeignKey' => 'setor_id',
            'conditions' => [
                'Setores.deletado' => false
            ]
        ]);

    }

    public function todosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type)
            ->where([
                $this->alias() . '.grupo_id' => (int)$usuario['grupo_id']
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
            ->notEmpty('nome');
        $validator
            ->integer('grupo_id')
            ->requirePresence('grupo_id', 'create')
            ->notEmpty('grupo_id');
        $validator
            ->requirePresence('cep', 'create')
            ->notEmpty('cep');
        $validator
            ->requirePresence('endereco', 'create')
            ->notEmpty('endereco');
        $validator
            ->requirePresence('bairro', 'create')
            ->notEmpty('bairro');
        $validator
            ->integer('cidade_id')
            ->requirePresence('cidade_id', 'create')
            ->notEmpty('cidade_id');
        $validator
            ->latitude('lat')
            ->requirePresence('lat', 'create')
            ->notEmpty('lat');
        $validator
            ->longitude('lng')
            ->requirePresence('lng', 'create')
            ->notEmpty('lng');
        $validator
            ->boolean('ativo')
            ->requirePresence('ativo', 'create')
            ->notEmpty('ativo');
        $validator
            ->boolean('deletado')
            ->requirePresence('deletado', 'create')
            ->notEmpty('deletado');
        $validator
            ->integer('criado_por_id')
            ->requirePresence('criado_por_id', 'create')
            ->notEmpty('criado_por_id');
        $validator
            ->integer('modificado_por_id')
            ->requirePresence('modificado_por_id')
            ->notEmpty('modificado_por_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add(new UniqueOnGrupoRule($this, 'nome'), 'uniqueOnGrupo', [
            'errorField' => 'nome',
            'message' => 'Já existe outra Loja cadastrando com este nome.'
        ]);
        
        $rules->add($rules->existsIn('cidade_id', 'Cidades'));

        $rules->add(new FkBelongsToGrupoRule($this->CriadoPor, 'criado_por_id'), 'fkBelongsToGrupo', [
            'errorField' => 'criado_por_id',
            'message' => 'Não pertence ao grupo.'
        ]);
        $rules->add(new FkBelongsToGrupoRule($this->ModificadoPor, 'modificado_por_id'), 'fkBelongsToGrupo', [
            'errorField' => 'modificado_por_id',
            'message' => 'Não pertence ao grupo.'
        ]);

        // Lojas atracados devem pertencer ao meu grupo
        $rules->add(new LinkedOneOrManyBelongsToGrupoRule('setores'), 'belongsToGrupo', [
            'errorField' => 'setores',
            'message' => 'Uma ou mais Setores selecionados são inválidas.'
        ]);

        return $rules;
    }

}
