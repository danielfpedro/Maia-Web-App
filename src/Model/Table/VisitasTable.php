<?php
namespace App\Model\Table;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;
use App\Model\Behavior\Shared;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Network\Session;
use Cake\Event\Event;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Validation\Validation;

use App\Model\Rule\FkBelongsToGrupoRule;

use Cake\I18n\Time;

/**
 * Visitas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Usuarios
 * @property \Cake\ORM\Association\BelongsTo $Checklists
 * @property \Cake\ORM\Association\BelongsTo $Grupos
 * @property \Cake\ORM\Association\BelongsTo $Lojas
 *
 * @method \App\Model\Entity\Visita get($primaryKey, $options = [])
 * @method \App\Model\Entity\Visita newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Visita[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Visita|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Visita patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Visita[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Visita findOrCreate($search, callable $callback = null, $options = [])
 */
class VisitasTable extends Table
{

    public $requerimentoLocalizacaoOptions = [
        1 => 'Nenhum',
        2 => 'Somente Localização (Imprecisão de até 200 metros)',
        3 => 'Localização e Internet (Preciso)'
    ];

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

        $this->setTable('visitas');
        $this->setDisplayField('id');
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
        $this->addBehavior('Shared', ['alias' => $this->alias()]);

        // Relacionamentos
        $this->belongsTo('QuemGravou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_novo_id'
        ]);
        $this->belongsTo('QuemModificou', [
            'className' => 'Usuarios',
            'foreignKey' => 'culpado_modificacao_id'
        ]);

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Auditor', [
            'className' => 'Usuarios',
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('UsuarioVinculado', [
            'className' => 'Usuarios',
            'foreignKey' => 'usuario_vinculado_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('ChecklistsTodas', [
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER'
        ]);
        
        $this->belongsTo('Checklists', [
            'className' => 'Checklists',
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER',
            'conditions' => [
              'Checklists.deletado' => false
            ]
        ]);

        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Lojas', [
            'foreignKey' => 'loja_id',
            'joinType' => 'INNER'
        ]);

        // As vezes quer pegar algunas respostas sem precisar ir na tabela
        // perguntas
        $this->hasMany('Respostas', [
            'className' => 'ChecklistsPerguntasRespostas',
            'foreignKey' => 'visita_id',
            'joinType' => 'LEFT',
            // 'saveStrategy' => 'replace'
        ]);

        $this->hasMany('RespostasCriticas', [
            'className' => 'ChecklistsPerguntasRespostas',
            'foreignKey' => 'visita_id',
            'joinType' => 'INNER',
            'conditions' => ['item_critico' => true]
        ]);

        $this->belongsToMany('GruposDeEmails');

        $this->hasMany('RespostasCriticoResolvido', [
            'className' => 'ChecklistsPerguntasRespostas',
            'foreignKey' => 'visita_id',
            'joinType' => 'LEFT',
            'conditions' => ['RespostasCriticoResolvido.critico_resolvido' => true]
        ]);

        $this->hasMany('FotosRequeridas', [
            'className' => 'ChecklistsPerguntasFotosRequeridas',
            'foreignKey' => 'visita_id',
            'joinType' => 'LEFT',
            'saveStrategy' => 'replace',
            'dependent' => true
        ]);

        $this->hasOne('PlanosTaticosPreInfos', [
            'className' => 'VisitasPlanosTaticosPreInfos',
            'foreignKey' => 'visita_id',
            'joinType' => 'LEFT',
            'saveStrategy' => 'replace',
            'dependent' => true
        ]);
    }

    // public function findDosMeusGruposDeAcessos(Query $query, array $options)
    // {
    //     if (!array_key_exists('user', $options)) {
    //         throw new \Exception("Você deve informar o user");
    //     }
    //     if (!array_key_exists('grupos_de_acessos_ids', $options['user'])) {
    //         throw new \Exception("Você deve informar o grupo_de_acessos do user");
    //     }
        
    //     if (empty($options['user']['grupos_de_acessos_ids'])) {
    //         return $query;
    //     }

    //     $subQueryGruposDeAcessos = $this->Checklists->find()
    //         ->select('Checklists.id')
    //         ->where(function ($exp, $q) {
    //             return $exp->equalFields('Checklists.id', 'Visitas.checklist_id');
    //         })
    //         ->notMatching('GruposDeAcessos');

    //     $query
    //         ->innerJoinWith('Checklists', function($q) {
    //             return $q->leftJoinWith('GruposDeAcessos');
    //         })
    //         ->where([
    //             'OR' => [
    //                 'GruposDeAcessos.id IN ' => $options['user']['grupos_de_acessos_ids'],
    //                 'Visitas.checklist_id IN' => $subQueryGruposDeAcessos
    //             ]
    //         ]);

    //     return $query;
    // }

    public function generateCod()
    {
        $letras = 'abcdefghijlmnopqrstuvxz';
        $numeros = '0123456789';

        $totalLetrasNoCod = 3;
        $totalNumerosNodCod = 4;

        $cod = [];
        for ($i=0; $i < $totalLetrasNoCod; $i++) { 
            $cod['letras'][] = substr($letras, rand(0, strlen($letras) - 1), 1);
        }
        for ($i=0; $i < $totalNumerosNodCod; $i++) { 
            $cod['numeros'][] = substr($numeros, rand(0, strlen($numeros) - 1), 1);
        }
        return strtoupper(join($cod['letras']) . '-' . join($cod['numeros']));
    }

    public function generateUniqueCodOnGrupo($grupoId)
    {
        do {

            $cod = $this->generateCod();

            $total = $this->find('all')
                ->where([
                    $this->alias() . '.grupo_id' => $grupoId,
                    $this->alias() . '.cod' => $cod,
                ])
                ->count();
        } while ($total > 0);

        return $cod;
    }

    public function todosVivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type)
            ->where([
                'Visitas.deletado' => false,
                'Visitas.grupo_id' => (int)$usuario['grupo_id']
            ]);
    }

    public function todosVivosEAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosVivosDoMeuGrupo($type, $usuario)
            ->where([
                'Visitas.ativo' => true
            ]);
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $entity->token_visualizar_publico = $this->getPublicToken(20);

            if (!$entity->grupo_id) {
                throw new \Exception("A visita teve conter grupo_id");
            }

            $entity->cod = $this->generateUniqueCodOnGrupo($entity->grupo_id);
        }
    }

    /**
     * Ativas, não respondidas e não vencidas
     */
    public function todasAtivasDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type, [
            'conditions' => [
                'Visitas.prazo >=' => Time::now()->format('Y-m-d'),
                'OR' => [
                    'Visitas.dt_encerramento IS' => null,
                    'Visitas.dt_encerramento =' => 'null',
                ],
                'Visitas.ativo' => true,
                'Visitas.grupo_id' => (int)$usuario['grupo_id']
            ]
        ]);
    }

    protected function _validaUsuarioIntegridade($value, $context)
    {

        $usuario = $this->Usuarios
            ->find('all')
            ->select([
                'id',
                'grupo_id'
            ])
            ->where([
                'id' => (int)$value,
                'ativo' => true,
                'deletado' => false
            ])
            ->first();

        if ($usuario && $usuario->grupo_id == (int)$context['data']['grupo_id']) {
            return true;
        }

        return false;
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (array_key_exists('prazo_placeholder', $data) && $data['prazo_placeholder']) {
            $data['prazo'] = Time::createFromFormat('d/m/Y', $data['prazo_placeholder']);
        }
    }

    public function getPublicToken($total)
    {
        $letras = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'z'];
        $numeros = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        $out = [];
        $letraLoop = true;
        for ($i=0; $i < $total; $i++) {
            if ($letraLoop) {
                $out[] = $letras[rand(0, count($letras) - 1)];
                $letraLoop = false;
            } else {
                $out[] = $numeros[rand(0, count($numeros) - 1)];
                $letraLoop = true;
            }
        }

        return join($out);
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
            ->allowEmpty('emails_criticos_extras');

        $validator
            ->integer('usuario_id')
            ->requirePresence('usuario_id', 'create')
            ->notEmpty('usuario_id')
            ->add('usuario_id', 'integridadeUsuario', [
                'rule' => function($value, $context) {
                    return $this->_validaUsuarioIntegridade($value, $context);
                },
                'message' => 'Usuário inválido'
            ]);

        $validator
            ->integer('checklist_id')
            ->requirePresence('checklist_id', 'create')
            ->notEmpty('checklist_id')
            ->add('checklist_id', 'integridadeChecklist', [
                'rule' => function($value, $context) {

                    $checklist = $this->Checklists
                        ->find('all')
                        ->select([
                            'id',
                            'grupo_id'
                        ])
                        ->where([
                            'id' => (int)$value,
                            'ativo' => true,
                        ])
                        ->first();

                    if ($checklist && $checklist->grupo_id == (int)$context['data']['grupo_id']) {
                        return true;
                    }

                    return false;
                },
                'message' => 'Checklist inválida'
            ]);

        $validator
            ->integer('loja_id')
            ->add('loja_id', 'integridadeLoja', [
                'rule' => function($value, $context) {

                    $loja = $this->Lojas
                        ->find('all')
                        ->select([
                            'id',
                            'grupo_id'
                        ])
                        ->where([
                            'id' => (int)$value,
                            'ativo' => true,
                        ])
                        ->first();

                    if ($loja && $loja->grupo_id == (int)$context['data']['grupo_id']) {
                        return true;
                    }

                    return false;
                },
                'message' => 'Loja inválida'
            ]);

        $validator
            ->integer('requerimento_localizacao')
            ->requirePresence('requerimento_localizacao', 'create')
            ->notEmpty('requerimento_localizacao')
            ->add('requerimento_localizacao', 'integridade', [
                'rule' => function($value) {
                    return (in_array($value, array_keys($this->requerimentoLocalizacaoOptions)));
                },
                'message' => 'Valor inválido'
            ]);


        $validator
            ->integer('usuario_vinculado_id')
            ->allowEmpty('usuario_vinculado_id')
            ->add('usuario_vinculado_id', 'integridadeUsuarioVinculado', [
                'rule' => function($value, $context) {
                    return $this->_validaUsuarioIntegridade($value, $context);
                },
                'message' => 'Usuário inválido'
            ]);

        $validator
            ->requirePresence('prazo_placeholder', 'create');

        $validator
            ->date('prazo');

        $validator
            ->allowEmpty('emails_resultados_extras')
            ->add('emails_resultados_extras', 'listaEmails', [
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
            ]);
        $validator
            ->allowEmpty('emails_criticos_extras')
            ->add('emails_criticos_extras', 'listaEmails', [
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
            ]);


        return $validator;
    }

    public function validationOnlyApi(Validator $validator) {
        $validator->remove('prazo_placeholder', 'requirePresence');

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
        $rules->add(function($entity) {
            if ($entity->validar_prazo && (empty($entity->prazo_placeholder) || $entity->prazo_placeholder == '' || is_null($entity->prazo_placeholder))) {
                return false;
            }

            return true;
        }, [
            'errorField' => 'prazo_placeholder',
            'message' => 'O Prazo não pode ficar em branco.'
        ]);

        $rules->add(function($entity) {
            if ($entity->validar_prazo && (empty($entity->prazo) || $entity->prazo == '' || is_null($entity->prazo))) {
                return false;
            }

            return true;
        }, [
            'errorField' => 'prazo',
            'message' => 'O Prazo não pode ficar em branco.'
        ]);

        $rules->add(function($entity) {
            if ($entity->grupos_de_emails) {
                foreach ($entity->grupos_de_emails as $grupo_de_email) {
                    if ($grupo_de_email->grupo_id != $entity->grupo_id) {
                        return false;
                    }
                }
            }

            return true;
        }, [
            'errorField' => 'grupos_de_emails',
            'message' => 'Um dos grupos de emails inseridos não existem.'
        ]);

        return $rules;
    }
}
