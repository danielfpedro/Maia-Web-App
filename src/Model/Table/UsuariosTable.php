<?php
namespace App\Model\Table;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM;
use Cake\Network\Session;
use Cake\Collection\Collection;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
use Cake\Utility\Security;
use Cake\I18n\Time;

// Behaviors
use App\Behavior\SharedBehavior;

use Firebase\JWT\JWT;

/**
 * Usuarios Model
 *
 * @method \App\Model\Entity\Usuario get($primaryKey, $options = [])
 * @method \App\Model\Entity\Usuario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Usuario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Usuario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Usuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Usuario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Usuario findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosTable extends Table
{

    // Cargos para usar nos filtros
    public $cargos = [
      1 => 'administrador',
      2 => 'auditor',
      3 => 'executante plano de ação',
      4 => 'controle plano de ação',
      5 => 'visitas',
      6 => 'cadastros gerais'
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

        // $session = new Session();

        $this->table('usuarios');
        $this->displayField('nome');
        $this->primaryKey('id');

        $this->addBehavior('Shared', ['alias' => $this->alias()]);

        $this->addBehavior('Timestamp', [
              'events' => [
                  'Model.beforeSave' => [
                      'criado_em' => 'new',
                      'modificado_em' => 'existing',
                  ],
              ]
          ]);

          // $this->addBehavior('ContaTudoParaSuaMaeKiko', [
          //     'culpado_id' => $session->read('Auth.Painel.id')
          // ]);

          $this->belongsTo('QuemGravou', [
              'className' => 'Usuarios',
              'foreignKey' => 'culpado_novo_id'
          ]);
          $this->belongsTo('QuemModificou', [
              'className' => 'Usuarios',
              'foreignKey' => 'culpado_modificacao_id'
          ]);

          /**
           * Tipo de JOIN é INNER pq ele é obrigado a estar sempre ligado a
           * um grupo
           */
          $this->belongsTo('Grupos', [
              'foreignKey' => 'grupo_id',
              'joinType' => 'INNER',
          ]);

          $this->belongsToMany('Lojas', [
            'joinType' => 'LEFT',
            'saveStrategy' => 'replace'
          ]);

        // BELONGS TO MANY
        $this->belongsToMany('Cargos', [
          'sort' => 'Cargos.ordem'
        ]);
        $this->belongsToMany('GruposDeAcessos');

    }

    public function findDosCargos(Query $query, $options)
    {
      return $query->matching('Cargos', function($q) use ($options) {
        return $q->where(['Cargos.id IN' => $this->getCargosIdsByNames($options['options']['cargos'])]);
      });
    }

    public function todosDoMeuGrupo($type = 'all', $usuario, $options = [])
    {
        return $this->find($type, $options)
            ->where([
                $this->alias() . '.grupo_id' => (int)$usuario['grupo_id']
            ]);
    }

    public function todosVivosDoMeuGrupo($type = 'all', $usuario, $options = [])
    {
        return $this->todosDoMeuGrupo($type, $usuario, $options)
            ->where([
                $this->alias() . '.deletado' => false,
            ]);
    }

    public function todosVivosEAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->todosVivosDoMeuGrupo($type, $usuario)
            ->where([
                $this->alias() . '.ativo' => true
            ]);
    }

    public function getCargosIdsByNames($names)
    {
      $out = [];

      foreach ($this->cargos as $cargoId => $cargo) {
        foreach ($names as $nameKey => $name) {
          if ($name == $cargo) {
            $out[] = $cargoId;
            unset($names[$nameKey]);
          }
        }
      }
      return $out;
    }

    /**
     * Crio o token e hash do email para enviar na url do email
     */
    public function patchEntityRedefinirSenha($entity)
    {
        $uuid = sha1(Text::uuid());

        $entity->redefinir_senha_token = Security::hash($uuid, 'sha256', true);
        $entity->redefinir_senha_email_hash = sha1($entity->email . rand(0, 100));
        $entity->redefinir_senha_timestamp = Time::now();

        return $entity;
    }

    public function getValidoById($id) {
        return $this
            ->find()
            ->where([
                'Usuarios.id' => (int)$id,
                'Usuarios.ativo' => true,
                'Usuarios.deletado' => false
            ]);
    }

    /**
     * Finder do auth do painel
     * Não preciso fazer nenhum tipo de verificação (ativo, deletado, grupo ativo)
     * pois temos uma camada extra depois que faz essa checagem.
     * @param  Query   $query
     * @param  array   $options
     * @return Query
     */
    public function findAuthPainel(Query $query, array $options)
    {
        $query
            ->select([
                'Usuarios.id',
                'Usuarios.nome',
                'Usuarios.email',
                'Usuarios.senha',
                'Usuarios.grupo_id'
            ])
            ->contain([
                'Cargos' => function($query) use ($options) {
                    return $query
                        ->select([
                            'Cargos.id',
                            'Cargos.nome',
                        ])
                        ->order(['Cargos.prioridade']);
                }
            ])
            ->contain([
                'GruposDeAcessos' => function($query) use ($options) {
                    return $query
                        ->select([
                            'GruposDeAcessos.id',
                            'GruposDeAcessos.nome',
                        ]);
                }
            ])
            ->innerJoinWith('Grupos', function($query) use ($options){
              return $query->where([
                  'Grupos.slug' => $options['grupo_slug'],
                  'Grupos.ativo' => true,
              ]);
            })
            ->where([
                'Usuarios.deletado' => false,
            ])
            ->group(['Usuarios.id']);
        return $query;
    }

    public function findApiAuth(Query $query, array $options)
    {
        $query
            ->select([
                'Usuarios.id',
                'Usuarios.nome',
                'Usuarios.email',
                'Usuarios.senha',
                'Usuarios.grupo_id',
            ])
            // Contain grupos para pegar cor e tal
            ->contain([
                'Grupos',
                'GruposDeAcessos',
                'Cargos'
            ])
            ->where([
                'Usuarios.ativo' => true,
                'Usuarios.deletado' => false,
            ])
            ->matching('Cargos', function($query) {
              // Somente auditor ou admin
              return $query->where(['Cargos.id IN' => [1, 2]]);
            })
            ->group(['Usuarios.id']);

        // Este finder é usado tanto para o ato de logar(gerar o token) quanto
        // para cada requisição com o token.
        // No getToken a gente tem o id do grupo para saber que aquele email informado
        // vai logar em qual grupo?
        // E depois não precisa, identificamos o grupo pelo id do usuario logado
        if ($options['validateGrupo']) {
            $query->where(['Usuarios.grupo_id' => (int)$options['grupo']]);
        }

        return $query;
    }

    public function generateJwt($usuario)
    {
        return JWT::encode(['sub' => (int)$usuario['id']], Security::salt());
    }

    public function todosAtivosDoMeuGrupo($type = 'all', $usuario)
    {
        return $this->find($type, [
            'conditions' => [
                'Usuarios.ativo' => true,
                'Usuarios.deletado' => false,
                'Usuarios.grupo_id' => (int)$usuario['grupo_id']
            ]
        ]);
    }

    public function customDelete($usuario)
    {
        $usuario->deletado = true;
        return $this->save($usuario);
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
            ->requirePresence('grupo_id', 'create')
            ->integer('grupo_id')
            ->notEmpty('grupo_id');

        $validator
          // ->requirePresence('cargos._ids', 'create')
          ->add('cargos', 'custom', [
            'rule' => function($value, $context) {
                return (!empty($value['_ids']) && is_array($value['_ids']));
            },
            'message' => 'Selecione ao menos um cargo'
        ]);

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->notEmpty('senha');
        $validator
            ->notEmpty('nova_senha');
        $validator
            ->notEmpty('senha_atual');
        $validator
            ->add('confirmar_nova_senha', 'confirmarNovaSenha', [
                'rule' => function($value, $context) {
                    return !($value != $context['data']['nova_senha']);
                },
                'message' => 'Você não confirmou a nova senha corretamente'
            ]);

        $validator
            ->boolean('ativo');

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
        $rules->add(function($entity) use ($rules) {
            // Só se estiver que valida
            if ($entity->exige_confirmar_senha_atual) {
                $usuario = $this->get($entity->id);
                // pego o usuario pq no controler eu já coloco o valor novo da senha
                // então tenho que ir no banc para pegar a senha atual de fato para coparar
                if ((new DefaultPasswordHasher)->check($entity->senha_atual, $usuario->senha)) {
                    return true;
                }

                return false;
            }
            return true;
        }, [
            'errorField' => 'senha_atual',
            'message' => 'Você não confirmou a sua senha atual corretamente.'
        ]);

        // Lojas do meu grupo, ativas e não deletadas
        $rules->add(function($entity) {
            // Lojas não é obrigado entao se o foreach não
            // tiver nada para passar ele retorna true;
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

        // Email único
        $rules->add(function($entity) use ($rules) {
            $conditions = [
                'email' => $entity->email,
                'deletado' => false,
                'grupo_id' => $entity->grupo_id
            ];

            if (!$entity->isNew()) {
                $conditions['email !='] = $entity->getOriginal('email');
            }
            return !($this->exists($conditions));
        }, [
            'errorField' => 'email',
            'message' => 'Este email já está sendo usado por outro usuário.'
        ]);

        // Por ultimo
        $rules->add(function($entity) use ($rules) {
            // Só se estiver que valida
            if ($entity->exige_confirmar_senha_atual) {
                $usuario = $this->get($entity->id);
                if ((new DefaultPasswordHasher)->check($entity->nova_senha, $usuario->senha)) {
                    return false;
                }

                return true;
            }
            return true;
        }, [
            'errorField' => 'nova_senha',
            'message' => 'A nova senha que você inseriu já é a sua senha atual.'
        ]);

        return $rules;
    }
}
