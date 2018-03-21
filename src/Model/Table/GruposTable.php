<?php

namespace App\Model\Table;

use App\Model\Behavior\ContaTudoParaSuaMaeKiko;
use App\Utility\DanielImage;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Session;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use WideImage\WideImage;
use Cake\I18n\Time;
use App\Model\Validation\KoletorProvider;

/**
 * Grupos Model
 *
 * @property |\Cake\ORM\Association\BelongsTo $Segmentos
 * @property \App\Model\Table\ChecklistsTable|\Cake\ORM\Association\HasMany $Checklists
 * @property |\Cake\ORM\Association\HasMany $Logs
 * @property \App\Model\Table\LojasTable|\Cake\ORM\Association\HasMany $Lojas
 * @property |\Cake\ORM\Association\HasMany $ModelosAlternativas
 * @property |\Cake\ORM\Association\HasMany $Setores
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\HasMany $Usuarios
 * @property |\Cake\ORM\Association\HasMany $Visitas
 *
 * @method \App\Model\Entity\Grupo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Grupo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Grupo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Grupo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Grupo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Grupo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Grupo findOrCreate($search, callable $callback = null, $options = [])
 */
class GruposTable extends Table
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

        $this->setTable('grupos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');
        
        // $session = new Session();
        // $this->addBehavior('ContaTudoParaSuaMaeKiko', [
        //     'culpado_id' => $session->read('Auth.User.id')
        // ]);


          $this->belongsTo('QuemGravou', [
              'className' => 'Usuarios',
              'foreignKey' => 'culpado_novo_id'
          ]);
          $this->belongsTo('QuemModificou', [
              'className' => 'Usuarios',
              'foreignKey' => 'culpado_modificacao_id'
          ]);

        $this->belongsTo('Segmentos', [
            'foreignKey' => 'segmento_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Checklists', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('Logs', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('Lojas', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('ModelosAlternativas', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('Setores', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('Usuarios', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->hasMany('Visitas', [
            'foreignKey' => 'grupo_id'
        ]);
        $this->belongsTo('Cidades', [
            'foreignKey' => 'cidade_id'
        ]);
    }

    // Fluxo padrao usar quando não tem nenhum particularidade
    public function saveImageDefault($fieldName, $file, $grupo)
    {
        $folderLogos = new Folder(WWW_ROOT . $grupo->getLogosDir(DS), true, 0755);
        
        if ($file['error'] != 0) {
            throw new BadRequestException("Ocorreu um erro ao fazer o upload da imagem NAVBAR LOGO");
        }

        // Não verifico nada pois valido no form
        $image = WideImage::load($file['tmp_name']);
        $image = DanielImage::resizeGreaterSideAndKeepingRatio($image, 400);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = md5(Time::now() . $file['tmp_name']) . '.' . $ext;

        $image->saveToFile($folderLogos->path . DS . $filename);

        $oldFilename = $grupo->$fieldName;
        $grupo->$fieldName = $filename;

        $oldFile = new File($folderLogos->path . DS . $oldFilename);
        $oldFile->delete();
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // Pego de novo pois vou savar dados novos
        $grupo = $this->get($entity->id);

        // Upload do Nvabar Login Logo 
        // Não é requerido no edit então se não trazer não precisa
        // Se existir e não for 4 que é sem arquivo
        if ($entity->navbar_logo_file_placeholder && $entity->navbar_logo_file_placeholder['error'] != 4) {
            $this->saveImageDefault('navbar_logo', $entity->navbar_logo_file_placeholder, $grupo)   ;
        }
        // Logo da navbar do App
        if ($entity->app_navbar_logo_file_placeholder && $entity->app_navbar_logo_file_placeholder['error'] != 4) {
            $this->saveImageDefault('app_navbar_logo', $entity->app_navbar_logo_file_placeholder, $grupo)   ;
        }
        // Logo da tela de login
        if ($entity->login_logo_file_placeholder && $entity->login_logo_file_placeholder['error'] != 4) {
            $this->saveImageDefault('login_logo', $entity->login_logo_file_placeholder, $grupo)   ;
        }

        // Grupo é um objecto e foi passado como refenrencia entao ele pega todas as alterações e aqu ia gente salva somente uma vez
        $this->saveOrFail($grupo);
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
            ->provider('koletor', new KoletorProvider());

        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');
        
        $validator
            ->requirePresence('nome_fantasia', 'create')
            ->notEmpty('nome_fantasia');

        $validator
            ->requirePresence('razao_social', 'create')
            ->notEmpty('razao_social');

        $validator
            ->allowEmpty('inscricao_estadual');

        $validator
            ->requirePresence('email_financeiro', 'create')
            ->email('email_financeiro')
            ->notEmpty('email_financeiro');

        $validator
            ->requirePresence('slug', 'create')
            ->notEmpty('slug');

        $validator
            ->requirePresence('segmento_id', 'create')
            ->allowEmpty('segmento_id');
        // Dados de Cobrança
        $validator        
            ->requirePresence('cnpj', 'create')
            ->notEmpty('cnpj')
            ->add('cnpj', 'cnpj', [
                'rule' => 'cnpj',
                'provider' => 'koletor',
                'message' => 'O CNPJ é inválido'
            ]);

        $validator
            ->requirePresence('nome_fantasia', 'create')
            ->notEmpty('nome_fantasia');
        $validator
            ->requirePresence('cep', 'create')
            ->notEmpty('cep');
        $validator
            ->integer('cidade_id')
            ->requirePresence('cidade_id', 'create')
            ->notEmpty('cidade_id');
        $validator
            ->requirePresence('bairro', 'create')
            ->notEmpty('bairro');
        $validator
            ->requirePresence('endereco', 'create')
            ->notEmpty('endereco');

        // NAVBAR
        $validator
            ->allowEmpty('navbar_logo_file_placeholder')
            ->add('navbar_logo_file_placeholder', 'file', [
                'rule' => ['mimeType', ['image/jpeg', 'image/png']],
                'message' => 'A extensão do arquivo deve ser JPG ou PNG'
            ]);
        $validator
            ->notEmpty('navbar_color')
            ->hexColor('navbar_color');

        $validator
            ->allowEmpty('navbar_font_color')
            ->hexColor('navbar_font_color');

        $validator
            ->integer('navbar_logo_width')
            ->allowEmpty('navbar_logo_width');

        $validator
            ->integer('navbar_logo_width')
            ->allowEmpty('navbar_logo_width');

        $validator
            ->integer('navbar_logo_margin_top')
            ->allowEmpty('navbar_logo_margin_top');

        // APP CUSTOMIZATION
        $validator
            ->allowEmpty('app_navbar_logo_file_placeholder')
            ->add('app_navbar_logo_file_placeholder', 'file', [
                'rule' => ['mimeType', ['image/jpeg', 'image/png']],
                'message' => 'A extensão do arquivo deve ser JPG ou PNG'
            ]);

        $validator
            ->notEmpty('app_navbar_color')
            ->hexColor('app_navbar_color');

        $validator
            ->notEmpty('app_navbar_font_color')
            ->hexColor('app_navbar_font_color');
            
        $validator
            ->notEmpty('app_statusbar_color')
            ->hexColor('app_statusbar_color');

        // TELA DE LOGIN
        $validator
            ->allowEmpty('app_navbar_logo_file_placeholder')
            ->add('app_navbar_logo_file_placeholder', 'file', [
                'rule' => ['mimeType', ['image/jpeg', 'image/png']],
                'message' => 'A extensão do arquivo deve ser JPG ou PNG'
            ]);
        $validator
            ->integer('login_logo_width')
            ->allowEmpty('login_logo_width');

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
        // Segmento deve existir e ser ativo
        $rules->add($rules->existsIn(['segmento_id'], 'Segmentos'));
        $rules->add($rules->existsIn(['cidade_id'], 'Cidades'));

        // CNPJ único
        $rules->add(function($entity) use ($rules) {
            $conditions = [
                'cnpj' => $entity->cnpj,
            ];

            if (!$entity->isNew()) {
                $conditions['cnpj !='] = $entity->getOriginal('cnpj');
            }
            return !($this->exists($conditions));
        }, [
            'errorField' => 'cnpj',
            'message' => 'Este CNPJ já está em uso por outra Rede.'
        ]);

        // Slug único
        $rules->add(function($entity) use ($rules) {
            $conditions = [
                'slug' => $entity->slug,
            ];

            if (!$entity->isNew()) {
                $conditions['slug !='] = $entity->getOriginal('slug');
            }
            return !($this->exists($conditions));
        }, [
            'errorField' => 'slug',
            'message' => 'Este slug já está em uso por outra Rede.'
        ]);

        // Nome único
        $rules->add(function($entity) use ($rules) {
            $conditions = [
                'nome' => $entity->nome,
            ];

            if (!$entity->isNew()) {
                $conditions['nome !='] = $entity->getOriginal('nome');
            }
            return !($this->exists($conditions));
        }, [
            'errorField' => 'nome',
            'message' => 'Este nome já está em uso por outra Rede.'
        ]);

        return $rules;
    }
}
