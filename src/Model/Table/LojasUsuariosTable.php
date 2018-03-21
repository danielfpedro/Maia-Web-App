<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LojasUsuarios Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Lojas
 * @property \Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\LojasUsuario get($primaryKey, $options = [])
 * @method \App\Model\Entity\LojasUsuario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LojasUsuario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LojasUsuario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LojasUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LojasUsuario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LojasUsuario findOrCreate($search, callable $callback = null, $options = [])
 */
class LojasUsuariosTable extends Table
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

        $this->table('lojas_usuarios');
        $this->displayField('loja_id');
        $this->primaryKey(['loja_id', 'usuario_id']);

        $this->belongsTo('Lojas', [
            'foreignKey' => 'loja_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['loja_id'], 'Lojas'));
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'));

        return $rules;
    }
}
