<?php
namespace App\Model\Table;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;
use App\Model\Behavior\Shared;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use ArrayObject;

use Cake\I18n\Time;

/**
 * Checklists Model
 *
 * @method \App\Model\Entity\Checklist get($primaryKey, $options = [])
 * @method \App\Model\Entity\Checklist newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Checklist[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Checklist|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Checklist patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Checklist[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Checklist findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsTable extends Table
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

        $session = new Session();

        $this->table('checklists');
        $this->displayField('nome');
        $this->primaryKey('id');

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
        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id'
        ]);

        $this->belongsTo('Segmentos');

        $this->hasMany('Perguntas', [
            'className' => 'ChecklistsPerguntas',
            'foreignKey' => 'checklist_id',
            'conditions' => ['Perguntas.deletado' => false]
        ]);

        $this->hasMany('Visitas', [
            'className' => 'Visitas',
            'foreignKey' => 'checklist_id',
        ]);
        
        $this->belongsToMany('GruposDeAcessos', [
            'through' => 'ChecklistsGruposDeAcessos'
        ]);

        $this->belongsToMany('GruposDeEmails');

        $this->hasMany('OrdemSetores', [
            'className' => 'ChecklistsPerguntasSetoresOrdem',
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER',
            'saveStrategy' => 'replace',
            'sort' => ['ordem' => 'ASC']
        ]);
    }

    public function findDosMeusGruposDeAcessos(Query $query, array $options)
    {
        if (!array_key_exists('grupos_de_acessos_ids', $options)) {
            throw new \Exception("Você deve informar os cargos do user");
        }

        // Se for admin retorna tudo e nem checa nada;
        if (in_array(1, $options['cargos_ids'])) {
            return $query;
        }

        if (!array_key_exists('grupos_de_acessos_ids', $options)) {
            throw new \Exception("Você deve informar o grupo_de_acessos do user");
        }
        

        $subQueryComGruposDeAcessosBatendo = $this
            ->find('doMeuGrupo', $options)
            ->select('Checklists.id')
            ->matching('GruposDeAcessos', function($query) use ($options) {
                return $query->where(['GruposDeAcessos.id IN' => $options['grupos_de_acessos_ids']]);
            })
            ->distinct('Checklists.id');

        $subQuerySemGruposDeAcessos = $this
            ->find('doMeuGrupo', $options)
            ->select('Checklists.id')
            ->notMatching('GruposDeAcessos')
            ->distinct('Checklists.id');

        $conditions = [
            'OR' => [
                ['Checklists.id IN' => $subQuerySemGruposDeAcessos],
            ]
        ];
        if ($options['grupos_de_acessos_ids']) {
            $conditions['OR'][] = ['Checklists.id IN' => $subQueryComGruposDeAcessosBatendo];
        }

        $query
            ->where($conditions);

        return $query;
    }

    public function findNaoDeletadas(Query $q)
    {
        return $q->where(['Perguntas.deletado' => false]);
    }

    public function findComTotalDePerguntas(Query $query, array $options)
    {
        return $query;
    }

    public function todosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type)
            ->where([
                'Checklists.grupo_id' => (int)$usuario['grupo_id']
            ])
            ->order(['Checklists.nome']);
    }

    public function getModelo($checklistId) {
        return $this->find()
            ->where([
                'Checklists.id' => $checklistId,
                // Grupo usado para modelos
                'Checklists.grupo_id' => 1,
                'Checklists.ativo' => true,
                'Checklists.deletado' => false
            ]);
    }

    public function todosAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosDoMeuGrupo($type, $usuario)
            ->where([
                'Checklists.ativo' => true,
            ]);
    }

    public function todosVivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosDoMeuGrupo($type, $usuario)
            ->where([
                'Checklists.deletado' => false
            ]);
    }

    public function todosVivosEAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosVivosDoMeuGrupo($type, $usuario)
            ->where([
                'Checklists.ativo' => true,
            ]);
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // Se for edit e tiver trocado o nome eu salvo o dt_modificado
        if (!$entity->isNew() && $entity->nome != $entity->getOriginal('nome')) {
            $entity->dt_modificado = Time::now();
        }

    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        // $session = new Session();
        //$data['grupo_id'] = $session->read('Auth.User.grupo_id');
    }

    public function totalVisitasEncerradas($checklist)
    {
        return $this->Visitas
          ->todosVivosDoMeuGrupo('all', ['grupo_id' => $checklist->grupo_id])
          ->where([
              'checklist_id' => $checklist->id,
              'dt_encerramento IS NOT' => null
          ])
          ->count();
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
            ->integer('grupo_id')
            ->requirePresence('grupo_id', 'create')
            ->notEmpty('grupo_id');

        $validator
            ->integer('minimo_esperado')
            ->requirePresence('minimo_esperado', 'create')
            ->notEmpty('minimo_esperado')
            ->add('minimo_esperado', 'valorMinimoEMaximo', [
                'rule' => function($value, $context) {
                    return ($value >= 0 && $value <= 100);
                },
                'message' => 'O Valor deve ser maior que 0 e menor que 100'
            ]);

        $validator
            ->integer('ativo');

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
            'message' => 'Já existe outro Questionário com este nome.'
        ]);

        // Somente o grupo de modelos de questionario pode inserir o segmento ao questionario
        // testo aqui
        //
        $rules->add(function($entity) use ($rules) {
            if ($entity->segmento_id) {
                return ($entity->grupo_id == 1);
            }
            return true;
        }, [
            'errorField' => 'segmento_id',
            'message' => 'Somente o grupo de modelo pode inserir segmento ao questionário.'
        ]);

        $rules->add(function($entity) {
            if ($entity->getOriginal('nome') != $entity->nome && $this->totalVisitasEncerradas($entity) > 0) {
                return false;
            }
            return true;
        }, [
            'errorField' => 'nome',
            'message' => 'Este campo não pode ser alterado pois uma ou mais visitas ligadas a este Questionário já foram encerradas.'
        ]);

        $rules->add(function($entity) {
            if ($entity->grupos_de_acessos) {
                foreach ($entity->grupos_de_acessos as $grupoDeAcesso) {
                    if ($grupoDeAcesso->grupo_id != $entity->grupo_id) {
                        return false;
                    }
                }
            }
            return true;
        });

        return $rules;
    }

}
