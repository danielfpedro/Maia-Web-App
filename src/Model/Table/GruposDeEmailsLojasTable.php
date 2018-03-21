<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GruposDeEmailsLojas Model
 *
 * @property \App\Model\Table\LojasTable|\Cake\ORM\Association\BelongsTo $Lojas
 * @property \App\Model\Table\GruposDeEmailsTable|\Cake\ORM\Association\BelongsTo $GruposDeEmails
 *
 * @method \App\Model\Entity\GruposDeEmailsLoja get($primaryKey, $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsLoja findOrCreate($search, callable $callback = null, $options = [])
 */
class GruposDeEmailsLojasTable extends Table
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

        $this->setTable('grupos_de_emails_lojas');
        $this->setDisplayField('loja_id');
        $this->setPrimaryKey(['loja_id', 'grupos_de_email_id']);

        $this->belongsTo('Lojas', [
            'foreignKey' => 'loja_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('GruposDeEmails', [
            'foreignKey' => 'grupos_de_email_id',
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
        $rules->add($rules->existsIn(['grupos_de_email_id'], 'GruposDeEmails'));

        return $rules;
    }
}
