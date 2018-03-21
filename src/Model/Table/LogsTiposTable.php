<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogsTipos Model
 *
 * @property \App\Model\Table\LogsTable|\Cake\ORM\Association\HasMany $Logs
 *
 * @method \App\Model\Entity\LogsTipo get($primaryKey, $options = [])
 * @method \App\Model\Entity\LogsTipo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LogsTipo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LogsTipo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LogsTipo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LogsTipo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LogsTipo findOrCreate($search, callable $callback = null, $options = [])
 */
class LogsTiposTable extends Table
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

        $this->setTable('logs_tipos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasMany('Logs', [
            'foreignKey' => 'logs_tipo_id'
        ]);
    }

    public function todosAtivos($tipo = 'all')
    {
        return $this->find($tipo)
            ->where(['LogsTipos.ativo' => true])
            ->order(['LogsTipos.nome']);
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
            ->requirePresence('ativo', 'create')
            ->notEmpty('ativo');

        $validator
            ->dateTime('criado_em')
            ->requirePresence('criado_em', 'create')
            ->notEmpty('criado_em');

        $validator
            ->dateTime('modificado_em')
            ->requirePresence('modificado_em', 'create')
            ->notEmpty('modificado_em');

        return $validator;
    }
}
