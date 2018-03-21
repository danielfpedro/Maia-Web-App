<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GruposDeEmailsVisitas Model
 *
 * @property \App\Model\Table\VisitasTable|\Cake\ORM\Association\BelongsTo $Visitas
 * @property \App\Model\Table\GruposDeEmailsTable|\Cake\ORM\Association\BelongsTo $GruposDeEmails
 *
 * @method \App\Model\Entity\GruposDeEmailsVisita get($primaryKey, $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmailsVisita findOrCreate($search, callable $callback = null, $options = [])
 */
class GruposDeEmailsVisitasTable extends Table
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

        $this->setTable('grupos_de_emails_visitas');
        $this->setDisplayField('visita_id');
        $this->setPrimaryKey(['visita_id', 'grupos_de_email_id']);

        $this->belongsTo('Visitas', [
            'foreignKey' => 'visita_id',
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

        $rules->add($rules->existsIn(['visita_id'], 'Visitas'));
        $rules->add($rules->existsIn(['grupos_de_email_id'], 'GruposDeEmails'));

        return $rules;
    }
}
