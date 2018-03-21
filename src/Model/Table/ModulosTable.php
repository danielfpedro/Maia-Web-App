<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Modulos Model
 *
 * @property \App\Model\Table\LogsTable|\Cake\ORM\Association\HasMany $Logs
 *
 * @method \App\Model\Entity\Modulo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Modulo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Modulo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Modulo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Modulo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Modulo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Modulo findOrCreate($search, callable $callback = null, $options = [])
 */
class ModulosTable extends Table
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

        $this->setTable('modulos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasMany('Logs', [
            'foreignKey' => 'modulo_id'
        ]);
    }

    public function todosAtivos($tipo = 'all')
    {
        return $this->find($tipo)
            ->where(['Modulos.ativo' => true])
            ->order(['Modulos.nome']);
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
            ->requirePresence('deletado', 'create')
            ->notEmpty('deletado');

        $validator
            ->dateTime('criado_em')
            ->requirePresence('criado_em', 'create')
            ->notEmpty('criado_em');

        $validator
            ->dateTime('modificado_em')
            ->allowEmpty('modificado_em');

        return $validator;
    }
}
