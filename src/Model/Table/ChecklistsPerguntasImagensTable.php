<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * ChecklistsPerguntasImagens Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ChecklistsPerguntas
 *
 * @method \App\Model\Entity\ChecklistsPerguntasImagen get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPerguntasImagen findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasImagensTable extends Table
{

    public $extensoesPermitidas = ['jpg', 'png'];
    // O tamanho que o maior lado da imagem deve conter
    public $maxSideSize = 1024;
    public $thumbSize = 300;

    /**
     * Máximo de fotos permitidas por pergunta
     */
    public $maximoPermitido = 10;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('checklists_perguntas_imagens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
          'events' => [
              'Model.beforeSave' => [
                  'criado_em' => 'new',
              ],
          ]
        ]);

        $this->belongsTo('ChecklistsPerguntasRespostas', [
            'foreignKey' => 'checklists_perguntas_resposta_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Perguntas', [
            'className' => 'ChecklistsPerguntas',
            'foreignKey' => 'checklists_pergunta_id',
        ]);
    }

    public function possoAcessar($id, $user)
    {
        $imagem = $this->find('all', [
            'conditions' => [
                'ChecklistsPerguntasImagens.id' => $id
            ],
            'contain' => ['Perguntas' => function($query) {
                return $query
                    ->contain(['Checklists']);
            }]
        ])
        ->first();

        return ($imagem && $imagem->pergunta->checklist->grupo_id == (int)$user['grupo_id']);
    }

    public function beforeDelete(Event $event, Entity $entity)
    {
        // dd($entity);
        $folder = new Folder(WWW_ROOT . 'files' . DS . 'checklists' . DS . 'requisitos' . DS);
        $imagem = new File($folder->path . $entity->nome_arquivo);
        $imagemQuadrada = new File($folder->path . 'quadrada_' . $entity->nome_arquivo);
        $imagem->delete();
        $imagemQuadrada->delete();
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
            ->requirePresence('nome_arquivo', 'create')
            ->notEmpty('nome_arquivo');

        // $validator
        //     ->requirePresence('salvo', 'create')
        //     ->notEmpty('salvo');

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
        $rules->add($rules->existsIn(['checklists_pergunta_id'], 'Perguntas'));

        return $rules;
    }
}
