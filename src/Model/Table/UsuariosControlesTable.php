<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsuariosControles Model
 *
 * @method \App\Model\Entity\UsuariosControle get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosControle newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosControle[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosControle|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosControle patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosControle[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosControle findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosControlesTable extends Table
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

        $this->setTable('usuarios_controles');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
              'events' => [
                  'Model.beforeSave' => [
                      'criado_em' => 'new',
                      'modificado_em' => 'existing',
                  ],
              ]
          ]);
    }

    public function findAuthCustom(Query $query, array $options)
    {
        $query
            ->select([
                'UsuariosControles.id',
                'UsuariosControles.nome',
                'UsuariosControles.email',
                'UsuariosControles.senha',
            ])
            ->where([
                'UsuariosControles.ativo' => true,
            ]);

        return $query;
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('senha', 'create')
            ->notEmpty('senha');

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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
