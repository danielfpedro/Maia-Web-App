<?php
namespace App\Model\Table;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Network\Session;
use Cake\Event\Event;
use ArrayObject;

use Cake\Database\Schema\TableSchema;

/**
 * ModelosAlternativas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Grupos
 * @property \Cake\ORM\Association\HasMany $ModelosAlternativasAlternativas
 *
 * @method \App\Model\Entity\ModelosAlternativa get($primaryKey, $options = [])
 * @method \App\Model\Entity\ModelosAlternativa newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativa[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativa|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ModelosAlternativa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativa[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ModelosAlternativa findOrCreate($search, callable $callback = null, $options = [])
 */
class ModelosAlternativasTable extends Table
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

        $session = new Session();

        $this->setTable('modelos_alternativas');
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
        $this->addBehavior('ContaTudoParaSuaMaeKiko', [
            'culpado_id' => $session->read('Auth.Painel.id')
        ]);

        // Relacionamentos
        $this->belongsTo('QuemGravou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_novo_id'
        ]);
        $this->belongsTo('QuemModificou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_modificacao_id'
        ]);

        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('AlternativasDosModelos', [
            'className' => 'ModelosAlternativasAlternativas',
            'foreignKey' => 'modelos_alternativa_id',
            'saveStrategy' => 'replace'
        ]);
        // $this->hasMany('Alternativas', [
        //     'className' => 'ModelosAlternativasAlternativas',
        //     'foreignKey' => 'modelos_alternativa_id',
        //     'saveStrategy' => 'replace'
        // ]);
    }

    public function todosAtivosDoMeuGrupo($type = 'all', $user)
    {
        return $this->find($type)
            ->where([
                'ModelosAlternativas.grupo_id' => $user['grupo_id'],
                'ModelosAlternativas.ativo' => true
            ]);
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        $session = new Session();
        $data['grupo_id'] = $session->read('Auth.Painel.grupo_id');
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
            ->integer('ativo')
            ->requirePresence('ativo', 'create')
            ->notEmpty('ativo');

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
        // $rules->add($rules->validCount('alternativas', 2, '>=', 'Você ter ao menos duas alternativas'));
        return $rules;
    }
}
