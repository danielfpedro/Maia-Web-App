<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlanosTaticosLogsTipos Model
 *
 * @property \App\Model\Table\PlanosTaticosLogsTable|\Cake\ORM\Association\HasMany $PlanosTaticosLogs
 *
 * @method \App\Model\Entity\PlanosTaticosLogsTipo get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlanosTaticosLogsTipo findOrCreate($search, callable $callback = null, $options = [])
 */
class PlanosTaticosLogsTiposTable extends Table
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

        $this->setTable('planos_taticos_logs_tipos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasOne('PlanosTaticosLogs', [
            'foreignKey' => 'planos_taticos_logs_tipo_id'
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
            ->allowEmpty('icon');

        return $validator;
    }
}
