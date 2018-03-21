<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;
use Cake\Validation\Validation;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use ArrayObject;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;

/**
 * GruposDeEmails Model
 *
 * @method \App\Model\Entity\GruposDeEmail get($primaryKey, $options = [])
 * @method \App\Model\Entity\GruposDeEmail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GruposDeEmail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GruposDeEmail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GruposDeEmail findOrCreate($search, callable $callback = null, $options = [])
 */
class GruposDeEmailsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $session = new Session();

        parent::initialize($config);

        $this->setTable('grupos_de_emails');
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

          $this->addBehavior('ContaTudoParaSuaMaeKiko', [
              'culpado_id' => $session->read('Auth.Painel.id')
          ]);

          $this->belongsTo('Grupos', [
              'foreignKey' => 'grupo_id',
              'joinType' => 'INNER',
          ]);

        $this->belongsToMany('Visitas');

        $this->belongsToMany('Checklists', [
        'through' => 'ChecklistsGruposDeEmails'
        ]);
        $this->hasMany('ChecklistsGruposDeEmails');

        $this->belongsToMany('Lojas');
        $this->hasMany('GruposDeEmailsLojas');
    }

    public function filtrarPorChecklist($query, $checklistId)
    {
        $subquery = $this->find()
            ->select()
            ->where(function ($exp, $q) {
                return $exp
                    ->equalFields('GruposDeEmails.id', 'ChecklistsGruposDeEmails.grupos_de_email_id');
            })
            ->andWhere([
                'ChecklistsGruposDeEmails.checklist_id' => $checklistId
            ]);

        $query
            ->leftJoinWith('Checklists', function($query) use ($checklistId) {
                return $query
                    ->where([
                        'Checklists.id' => $checklistId
                    ]);
            })
            ->where([
                'OR' => [
                    'GruposDeEmails.todas_as_checklists' => true,
                    function ($exp, $q) use ($subquery) {
                        return $exp->exists($subquery);
                    }
                ]
            ])
            ->group('GruposDeEmails.id');

        return $query;
    }

    public function filtrarPorLoja($query, $lojaId)
    {
        $subquery = $this->find()
            ->select()
            ->where(function ($exp, $q) {
                return $exp
                    ->equalFields('GruposDeEmails.id', 'GruposDeEmailsLojas.grupos_de_email_id');
            })
            ->andWhere([
                'ChecklistsGruposDeEmails.loja_id' => $lojaId
            ]);

        $query
            ->leftJoinWith('Lojas', function($query) use ($lojaId) {
                return $query
                    ->where([
                        'Lojas.id' => $lojaId
                    ]);
            })
            ->where([
                'OR' => [
                    'GruposDeEmails.todas_as_lojas' => true,
                    function ($exp, $q) use ($subquery) {
                        return $exp->exists($subquery);
                    }
                ]
            ])
            ->group('GruposDeEmails.id');

        return $query;
    }

    public function todosDoMeuGrupo($type = 'all', $usuario, $options = [])
    {
        return $this->find($type, $options)
            ->where([
                'GruposDeEmails.grupo_id' => (int)$usuario['grupo_id']
            ])
            ->order(['GruposDeEmails.nome']);
    }

    public function todosVivosDoMeuGrupo($type = 'all', $usuario, $options = [])
    {
        return $this->todosDoMeuGrupo($type, $usuario, $options)
            ->where([
                'GruposDeEmails.deletado' => false,
            ]);
    }

    // Se ele não salvou nenhum loja ou checklist a gente coloca 
    // a flag
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $entity->todas_as_lojas = (!$entity->lojas);
        $entity->todas_as_checklists = (!$entity->checklists);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validation = new Validation();

        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->requirePresence('grupo_id', 'create')
            ->integer('grupo_id')
            ->notEmpty('grupo_id');

        $validator
            ->requirePresence('emails_resultados', 'create')
            ->add('emails_resultados', 'listaEmails', [
                'rule' => function ($value, $context) use ($validation) {

                    $emails = explode(',', $value);
                    foreach ($emails as $key => $email) {
                        $email = trim($email);
                        if ($email && !$validation->email($email)) {
                            return false;
                        }
                    }
                    return true;
                },
                'message' => 'Um ou mais emails informados são inválidos.'
            ])
            ->allowEmpty('emails_resultados', function($context) {
                return !empty($context['data']['emails_criticos']);
            },
            'Você deve informar ao menos um email para resultados OU críticos mas nunca deixar os dois em branco.'
            );

        $validator
            ->requirePresence('emails_criticos', 'create')
            ->allowEmpty('emails_criticos')
            ->add('emails_criticos', 'listaEmails', [
                'rule' => function ($value, $context) use ($validation) {

                    $emails = explode(',', $value);
                    foreach ($emails as $key => $email) {
                        $email = trim($email);
                        if ($email && !$validation->email($email)) {
                            return false;
                        }
                    }
                    return true;
                },
                'message' => 'Um ou mais emails informados são inválidos.'
            ])
            ->allowEmpty('emails_criticos', function($context) {
                return !empty($context['data']['emails_resultados']);
            },
            'Você deve informar ao menos um email para resultados OU críticos mas nunca deixar os dois em branco.'
            );

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add(function($entity) use ($rules) {
            $conditions = [
                'nome' => $entity->nome,
                'deletado' => false,
                'grupo_id' => $entity->grupo_id
            ];

            if (!$entity->isNew()) {
                $conditions['nome !='] = $entity->getOriginal('nome');
            }
            return !($this->exists($conditions));
        }, [
            'errorField' => 'nome',
            'message' => 'Já existe outro Grupos de Emails com este nome.'
        ]);

        $rules->add(function($entity) {
            $out = true;
            if ($entity->lojas) {
                foreach ($entity->lojas as $loja) {
                    if ($loja->grupo_id != $entity->grupo_id || $loja->deletado || !$loja->ativo) {
                        $out = false;
                        return $out;
                    }
                }
            }
            return $out;
        }, [
            'errorField' => 'lojas',
            'message' => 'Uma ou mais lojas selecionadas são inválidas.'
        ]);

        $rules->add(function($entity) {
            $out = true;
            if ($entity->checklists) {
                foreach ($entity->checklists as $checklist) {
                    if ($checklist->grupo_id != $entity->grupo_id || $checklist->deletado || !$checklist->ativo) {
                        $out = false;
                        return $out;
                    }
                }
            }
            return $out;
        }, [
            'errorField' => 'lojas',
            'message' => 'Uma ou mais Questionários selecionados são inválidos.'
        ]);

        return $rules;
    }

}
