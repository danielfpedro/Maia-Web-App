<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Network\Exception\BadRequestException;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use ArrayObject;

use Cake\I18n\Time;
use Cake\Collection\Collection;

/**
 * ChecklistsPerguntas Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Checklists
 *
 * @method \App\Model\Entity\ChecklistsPergunta get($primaryKey, $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ChecklistsPergunta findOrCreate($search, callable $callback = null, $options = [])
 */
class ChecklistsPerguntasTable extends Table
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

        $this->setTable('checklists_perguntas');
        $this->setDisplayField('pergunta');
        $this->setPrimaryKey('id');

        $this->belongsTo('Checklists', [
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Setores', [
            'foreignKey' => 'setor_id',
            'propertyName' => 'setor',
            'joinType' => 'INNER'
        ]);

        $this->hasOne('Respostas', [
            'className' => 'ChecklistsPerguntasRespostas',
            'foreignKey' => 'checklists_pergunta_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('Alternativas', [
            'className' => 'ChecklistsPerguntasAlternativas',
            'foreignKey' => 'checklists_pergunta_id',
            'propertyName' => 'alternativas',
            'joinType' => 'LEFT',
            'saveStrategy' => 'replace',
        ]);

        $this->hasMany('Imagens', [
            'className' => 'ChecklistsPerguntasImagens',
            'foreignKey' => 'checklists_pergunta_id',
            'propertyName' => 'imagens',
            'joinType' => 'LEFT',
            // 'conditions' => ['Imagens.salvo' => 1],
            'saveStrategy' => 'replace'
        ]);
        $this->hasMany('FotosRequeridas', [
            'className' => 'ChecklistsPerguntasFotosRequeridas',
            'foreignKey' => 'checklists_pergunta_id',
            'joinType' => 'LEFT',
            'saveStrategy' => 'replace',
            'dependent' => true
        ]);
    }


    // Regras especificas para alteração da pergunta
    public function validaAlteracaoPerguntaComVisitaEncerrada($pergunta)
    {
        $perguntaClone = clone $pergunta;
        $msgPadrao = 'pois este Questionário possui uma ou mais visitas já encerradas.';
        if ($perguntaClone->pergunta != $perguntaClone->getOriginal('pergunta')) {
            throw new BadRequestException("A Pergunta não pode ser alterada " . $msgPadrao);
        }
        if ($perguntaClone->setor_id != $perguntaClone->getOriginal('setor_id')) {
            throw new BadRequestException("O setor da pergunta não pode ser alterada " . $msgPadrao);
        }
        if ($perguntaClone->tipo != $perguntaClone->getOriginal('tipo')) {
            throw new BadRequestException("O tipo da pergunta não pode ser alterada " . $msgPadrao);
        }
        // O original só é preenchido quando tem mudança, ou seja... se existir
        // no original significa que mudou entao eu sei que mudou e consigo controlar
        // experto correto?
        foreach ($perguntaClone->alternativas as $alternativa) {
            if ($alternativa->getOriginal('alternativa') != $alternativa->alternativa) {
                throw new BadRequestException("A descrição de nenhuma alternativa pode ser alterada " . $msgPadrao);
            }
            if ($alternativa->getOriginal('valor') != $alternativa->valor) {
                throw new BadRequestException("O valor de nenhuma alternativa pode ser alterado " . $msgPadrao);
            }
        }

        // Verifico se nenhuma alternativa foi deletada
        $alternativasOriginaisIds = (new Collection($perguntaClone->getOriginal('alternativas')))->extract('id')->toArray();
        $alternativasPatchIds = (new Collection($perguntaClone->alternativas))->extract('id')->toArray();

        // Todos os ids do original devem ser mantidos no patch
        foreach ($alternativasOriginaisIds as $id) {
            if (!in_array($id, $alternativasPatchIds)) {
                throw new BadRequestException("Nenhum alternativa pode ser deletada " . $msgPadrao);
            }
        }
    }

    public function possoAcessarPergunta($id, $user)
    {
        $pergunta = $this->get($id);

        if ($pergunta) {
            $checklist = $this->Checklists->get($pergunta->checklist_id);
            return ($checklist && $checklist->grupo_id == (int)$user['grupo_id']);
        }

        return false;

    }

    public function comRespostasCriticas($visitaId) {

        $perguntas = $this->find()
            ->select([
                'Perguntas.id',
                'Perguntas.pergunta',
            ])
            ->contain([
                'Alternativas' => function($query) {
                    return $query
                        ->select([
                            'Alternativas.id',
                            'Alternativas.alternativa',
                            'Alternativas.checklists_pergunta_id'
                        ]);
                },
                'Respostas' => function($query) use ($visitaId) {
                    return $query
                        ->select([
                            'Respostas.id',
                            'Respostas.observacao',
                            'Respostas.checklists_perguntas_alternativa_id'
                        ])
                        ->contain([
                            'Alternativas' => function($query) {
                                return $query
                                    ->select([
                                        'Alternativas.id',
                                        'Alternativas.checklists_pergunta_id'
                                    ])
                                    ->where([
                                        'Alternativas.item_critico' => 1
                                    ]);
                            }
                        ])
                        ->where([
                            'Respostas.visita_id' => $visitaId
                        ]);
                }
            ]);

        foreach ($perguntas as $pergunta) {
            foreach ($pergunta->alternativas as $alternativa) {
                if ($pergunta->resposta && $alternativa->id == $pergunta->resposta->checklists_perguntas_alternativa_id) {
                    $alternativa->selecionada = true;
                } else {
                    $alternativa->selecionada = false;
                }
            }
        }

        return $perguntas;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // Toda adição de pergunta devo alterar a dt alteração da checklist
        $checklist = $this->Checklists->get($entity->checklist_id);
        $checklist->dt_modificado = Time::now();
        $this->Checklists->saveOrFail($checklist);
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
            ->requirePresence('pergunta', 'create')
            ->notEmpty('pergunta');

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
            // Pego a checklist para saber o grupo dele e validar o setor da pergunta
            // Lembrando que já validei a checklist no controller então posso usar o grupo_id
            // dela de boa.
            $checklist = $this->Checklists->get($entity->checklist_id);
            $setor = $this->Setores->get($entity->setor_id);

            if ($checklist->grupo_id != $setor->grupo_id) {
                return false;
            }

            return true;

        }, [
            'errorField' => 'setor_id',
            'message' => 'Setor inexistente.'
        ]);

        // Se for tipo 01 ao menos uma alternativa
        $rules->add(function($entity, $options) {
            if ($entity->tipo == 1 && (!$entity->alternativas || count($entity->alternativas) < 2)) {
                return false;
            }

            return true;

        }, [
            'errorField' => 'alternativas',
            'message' => 'Perguntas do tipo múltipla escolha devem conter ao menos duas alternativas.'
        ]);

        return $rules;
    }
}
