<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ChecklistsGruposDeEmails Model
 *
 * @property \App\Model\Table\ChecklistsTable|\Cake\ORM\Association\BelongsTo $Checklists
 * @property \App\Model\Table\GruposDeEmailsTable|\Cake\ORM\Association\BelongsTo $GruposDeEmails
 *
 * @method \App\Model\Entity\ChecklistsGruposDeEmail get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsGruposDeEmail findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsGruposDeEmailsTable extends Table
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

        $this->setTable('checklists_grupos_de_emails');
        $this->setDisplayField('checklist_id');
        $this->setPrimaryKey(['checklist_id', 'grupos_de_email_id']);

        $this->belongsTo('Checklists', [
            'foreignKey' => 'checklist_id',
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
        $rules->add($rules->existsIn(['checklist_id'], 'Checklists'));
        $rules->add($rules->existsIn(['grupos_de_email_id'], 'GruposDeEmails'));

        return $rules;
    }
}
