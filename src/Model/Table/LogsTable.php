<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Logs Model
 *
 * @property \App\Model\Table\LogsTiposTable|\Cake\ORM\Association\BelongsTo $LogsTipos
 * @property \App\Model\Table\ModulosTable|\Cake\ORM\Association\BelongsTo $Modulos
 *
 * @method \App\Model\Entity\Log get($primaryKey, $options = [])
 * @method \App\Model\Entity\Log newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Log[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Log|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Log patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Log[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Log findOrCreate($search, callable $callback = null, $options = [])
 */
class LogsTable extends Table
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

        $this->setTable('logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Behaviors
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                ],
            ]
        ]);

        $this->belongsTo('LogsTipos', [
            'foreignKey' => 'logs_tipo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Modulos', [
            'foreignKey' => 'modulo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Grupos', [
            'foreignKey' => 'grupo_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Autores', [
            'className' => 'Usuarios',
            'propertyName' => 'autor',
            'foreignKey' => 'autor_id',
            'joinType' => 'INNER'
        ]);
    }

    public function todosDoMeuGrupo($tipo = 'all', $usuario)
    {
        return $this->find($tipo)
            ->where(['Logs.grupo_id' => (int)$usuario['grupo_id']])
            ->order(['Logs.criado_em' => 'DESC']);
    }

    /**
     * Recebe o cod da das opções de localização e retorno o texto
     * @param  int $id
     * @return string
     */
    public function getLocalizacaoDescricao($id)
    {
        $out = null;

        switch ($id) {
            case 1:
                $out = 'Nenhum';
                break;
            case 2:
                $out = 'Somente Localização (Imprecisão de até 200 metros.)';
                break;
            case 3:
                $out = 'Localização Internet (Preciso)';
                break;
        }

        return $out;
    }

    public function customSave($moduloId, $entity)
    {
        if (!$entity->criado_por_id && !$entity->modificado_por_id && !$entity->grupo_id) {
                return;
        }

        $logTipoId = 3;
        if ($entity->getOriginal('deletado') == false && $entity->deletado == true) {
            $logTipoId = 1;
        } elseif(!$entity->isNew()) {
            $logTipoId = 2;
        }

        $dataLog = [
            'modulo_id' => $moduloId,
            'logs_tipo_id' => $logTipoId,
            'ref' => $entity->id,
            'autor_id' => ($entity->isNew()) ? $entity->criado_por_id : $entity->modificado_por_id,
            'grupo_id' => $entity->grupo_id,
        ];
        $dataLog['descricao'] = $this->getDescricao($dataLog, $entity);

        $log = $this->newEntity();

        // Eu nego o patch de todos os campos na Entity Log para ninguem altear o log por form
        // por isso aqui que tenho que setar tudo com set
        $log->set('descricao', $dataLog['descricao']);
        $log->set('ref', $dataLog['ref']);
        $log->set('logs_tipo_id', $dataLog['logs_tipo_id']);
        $log->set('modulo_id', $dataLog['modulo_id']);
        $log->set('descricao', $dataLog['descricao']);
        $log->set('autor_id', $dataLog['autor_id']);
        $log->set('grupo_id', $dataLog['grupo_id']);

        $saveResult = $this->save($log);

        return ['saveResult' => $saveResult, 'entity' => $log];
    }

    /**
     * Gera uma descrição de acordo com o tipo do log "inclusao, edicao..." usando os dados da entity que acabou de ser alterada
     * @param  array $data              Dados sobre o log  
     * @param  object $entityFoiAlterada entidade que foi alterada e será registrada no log
     * @return string                    descricao
     */
    public function getDescricao($data, $entityFoiAlterada)
    {
        $out = null;

        switch ($data['modulo_id']) {
            case 1:
                switch ($data['logs_tipo_id']) {
                    case 1:
                        $out = __('Filial <strong>{0}</strong> usando o questionário <strong>{1}</strong> com prazo para <strong>{2}</strong> para o auditor <strong>{3}</strong>',
                            $entityFoiAlterada->loja->nome,
                            $entityFoiAlterada->checklist->nome,
                            (($entityFoiAlterada->prazo) ? $entityFoiAlterada->prazo->format('d/m/y') : '-'),
                            $entityFoiAlterada->usuario->nome);
                        break;
                    case 2:
                        $localizacaoAntes = $this->getLocalizacaoDescricao($entityFoiAlterada->getOriginal('requerimento_localizacao'));
                        $localizacaoDepois = $this->getLocalizacaoDescricao($entityFoiAlterada->requerimento_localizacao);

                        $out = '
                            <dl>
                                <dt>Requisitos de localização</dt>
                                <dd>De <strong>'.$localizacaoAntes.'</strong> para <strong>'.$localizacaoDepois.'</strong></dd>
                              <dt>Prazo</dt>
                              <dd>De '.(($entityFoiAlterada->getOriginal('prazo')) ? '<strong>' . $entityFoiAlterada->getOriginal('prazo')->format('d/m/y') . '</strong>' : '<strong>-</strong>').' para '.(($entityFoiAlterada->prazo) ? '<strong>' . $entityFoiAlterada->prazo->format('d/m/y') . '</strong>' : '<strong>-</strong>').'</dd>
                              <dt>Status</dt>
                              <dd>De '.(($entityFoiAlterada->getOriginal('ativo')) ? '<strong>Ativo</strong>' : '<strong>Inativo</strong>').' para '.(($entityFoiAlterada->ativo) ? '<strong>Ativo</strong>' : '<strong>Inativo</strong>').'</dd>
                            </dl>
                        ';
                        break;
                    case 3:
                        $out = __('Filial <strong>{0}</strong> usando o questionário <strong>{1}</strong> com prazo para <strong>{2}</strong> para o auditor <strong>{3}</strong>',
                            $entityFoiAlterada->loja->nome,
                            $entityFoiAlterada->checklist->nome,
                            (($entityFoiAlterada->prazo) ? $entityFoiAlterada->prazo->format('d/m/y') : '-'),
                            $entityFoiAlterada->usuario->nome);
                        break;
                }
                break;
            // Usuários
            case 2:
                switch ($data['logs_tipo_id']) {
                    //DELETE
                    case 1:
                        $out = '
                            <dl>
                              <dt>Criado Por</dt>
                              <dd>'.(($entityFoiAlterada->quem_gravou) ? $entityFoiAlterada->quem_gravou->short_name : '-').'</dd>
                              <dt>Nome</dt>
                              <dd>'.$entityFoiAlterada->short_name.'</dd>
                              <dt>Email</dt>
                              <dd>'.$entityFoiAlterada->email.'</dd>
                            </dl>
                        ';
                        break;
                    // EDIT
                    case 2:
                        $nomeDiferente = ($entityFoiAlterada->getOriginal('nome') != $entityFoiAlterada->nome);
                        $emailDiferente = ($entityFoiAlterada->getOriginal('email') != $entityFoiAlterada->email);

                        $nomeIdentificacao = ($nomeDiferente) ? __('De <strong>{0}</strong> para <strong>{1}</strong>', $entityFoiAlterada->getOriginal('nome'), $entityFoiAlterada->nome) : $entityFoiAlterada->nome;
                        $emailIdentificacao = ($emailDiferente) ? __('De <strong>{0}</strong> para <strong>{1}</strong>', $entityFoiAlterada->getOriginal('email'), $entityFoiAlterada->email) : $entityFoiAlterada->email;

                        $out = '
                            <dl>
                              <dt>Criado</dt>
                              <dd>'.(($entityFoiAlterada->quem_gravou) ? $entityFoiAlterada->quem_gravou->short_name : '-').'</dd>
                              <dt>Nome</dt>
                              <dd>'.$nomeIdentificacao.'</dd>
                              <dt>Email</dt>
                              <dd>'.$emailIdentificacao.'</dd>
                            </dl>
                            '.(($nomeDiferente || $emailDiferente) ? '<p><em>Obs.: Outras informações também podem ter sido alteradas.</em></p>' : '').'
                        ';
                        break;
                    // ADD
                    case 3:
                        $out = '
                            <dl>
                              <dt>Nome</dt>
                              <dd>'.$entityFoiAlterada->short_name.'</dd>
                              <dt>Email</dt>
                              <dd>'.$entityFoiAlterada->email.'</dd>
                            </dl>
                        ';
                        break;
                }
                break;
            // Setores
            case 3:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entityFoiAlterada);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entityFoiAlterada);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entityFoiAlterada);
                        break;
                }
                break;
            // Lojas
            case 4:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entityFoiAlterada);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entityFoiAlterada);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entityFoiAlterada);
                        break;
                }
                break;
            // Modelos de Alternativas
            case 5:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entityFoiAlterada);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entityFoiAlterada);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entityFoiAlterada);
                        break;
                }
                break;
            // Questionários
            case 6:
                switch ($data['tipo_descricao']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entityFoiAlterada);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entityFoiAlterada);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entityFoiAlterada);
                        break;
                    // ADD Pergunta
                    case 5:
                        $out = __('Pergunta "<strong>{0}</strong>" no questionário "<strong>{1}</strong>"', $entityFoiAlterada->pergunta, $data['extra']['checklistNome']);
                        break;
                    // EDIT Pergunta
                    case 6:
                        $diferente = ($entityFoiAlterada->getOriginal('pergunta') != $entityFoiAlterada->pergunta);
                        $identificacao = (!$diferente) ? '"<strong>' . $entityFoiAlterada->pergunta . '</strong>"' : 'De "<strong>'.$entityFoiAlterada->getOriginal('pergunta').'</strong>" para "<strong>'.$entityFoiAlterada->pergunta . '</strong>"';
                        $out = __('Pergunta {0} no questionário <strong>{1}</strong>', $identificacao, $data['extra']['checklistNome']);

                        if ($diferente) {
                          $out .= '<p><em>Obs.: Outras informações também podem ter sido alteradas.</p></em>';
                        }
                        break;
                    // DELETE Pergunta
                    case 7:
                        $out = __('Pergunta "<strong>{0}</strong>" no questionário "<strong>{1}</strong>"', $entityFoiAlterada->pergunta, $entity->checklist->nome);
                        break;
                }
                break;
        }

        return trim($out);
    }


    // Deletar
    public function patchData(array $data, $entity)
    {
        $out = null;

        switch ($data['modulo_id']) {
            case 1:
                switch ($data['logs_tipo_id']) {
                    case 1:
                        $out = __('Filial <strong>{0}</strong> usando o questionário <strong>{1}</strong> com prazo para <strong>{2}</strong> para o auditor <strong>{3}</strong>',
                            $entity->loja->nome,
                            $entity->checklist->nome,
                            (($entity->prazo) ? $entity->prazo->format('d/m/y') : '-'),
                            $entity->usuario->nome);
                        break;
                    case 2:
                        $localizacaoAntes = $this->getLocalizacaoDescricao($entity->getOriginal('requerimento_localizacao'));
                        $localizacaoDepois = $this->getLocalizacaoDescricao($entity->requerimento_localizacao);

                        $out = '
                            <dl>
                                <dt>Requisitos de localização</dt>
                                <dd>De <strong>'.$localizacaoAntes.'</strong> para <strong>'.$localizacaoDepois.'</strong></dd>
                              <dt>Prazo</dt>
                              <dd>De '.(($entity->getOriginal('prazo')) ? '<strong>' . $entity->getOriginal('prazo')->format('d/m/y') . '</strong>' : '<strong>-</strong>').' para '.(($entity->prazo) ? '<strong>' . $entity->prazo->format('d/m/y') . '</strong>' : '<strong>-</strong>').'</dd>
                              <dt>Status</dt>
                              <dd>De '.(($entity->getOriginal('ativo')) ? '<strong>Ativo</strong>' : '<strong>Inativo</strong>').' para '.(($entity->ativo) ? '<strong>Ativo</strong>' : '<strong>Inativo</strong>').'</dd>
                            </dl>
                        ';
                        break;
                    case 3:
                        $out = __('Filial <strong>{0}</strong> usando o questionário <strong>{1}</strong> com prazo para <strong>{2}</strong> para o auditor <strong>{3}</strong>',
                            $entity->loja->nome,
                            $entity->checklist->nome,
                            (($entity->prazo) ? $entity->prazo->format('d/m/y') : '-'),
                            $entity->usuario->nome);
                        break;
                }
                break;
            // Usuários
            case 2:
                switch ($data['logs_tipo_id']) {
                    //DELETE
                    case 1:
                        $out = '
                            <dl>
                              <dt>Criado Por</dt>
                              <dd>'.(($entity->quem_gravou) ? $entity->quem_gravou->short_name : '-').'</dd>
                              <dt>Nome</dt>
                              <dd>'.$entity->short_name.'</dd>
                              <dt>Email</dt>
                              <dd>'.$entity->email.'</dd>
                            </dl>
                        ';
                        break;
                    // EDIT
                    case 2:
                        $nomeDiferente = ($entity->getOriginal('nome') != $entity->nome);
                        $emailDiferente = ($entity->getOriginal('email') != $entity->email);

                        $nomeIdentificacao = ($nomeDiferente) ? __('De <strong>{0}</strong> para <strong>{1}</strong>', $entity->getOriginal('nome'), $entity->nome) : $entity->nome;
                        $emailIdentificacao = ($emailDiferente) ? __('De <strong>{0}</strong> para <strong>{1}</strong>', $entity->getOriginal('email'), $entity->email) : $entity->email;

                        $out = '
                            <dl>
                              <dt>Criado</dt>
                              <dd>'.(($entity->quem_gravou) ? $entity->quem_gravou->short_name : '-').'</dd>
                              <dt>Nome</dt>
                              <dd>'.$nomeIdentificacao.'</dd>
                              <dt>Email</dt>
                              <dd>'.$emailIdentificacao.'</dd>
                            </dl>
                            '.(($nomeDiferente || $emailDiferente) ? '<p><em>Obs.: Outras informações também podem ter sido alteradas.</em></p>' : '').'
                        ';
                        break;
                    // ADD
                    case 3:
                        $out = '
                            <dl>
                              <dt>Nome</dt>
                              <dd>'.$entity->short_name.'</dd>
                              <dt>Email</dt>
                              <dd>'.$entity->email.'</dd>
                            </dl>
                        ';
                        break;
                }
                break;
            // Setores
            case 3:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entity);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entity);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entity);
                        break;
                }
                break;
            // Lojas
            case 4:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entity);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entity);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entity);
                        break;
                }
                break;
            // Modelos de Alternativas
            case 5:
                switch ($data['logs_tipo_id']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entity);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entity);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entity);
                        break;
                }
                break;
            // Questionários
            case 6:
                switch ($data['tipo_descricao']) {
                    // DELETE
                    case 1:
                        $out = $this->getDefaultText('delete', $entity);
                        break;
                    // EDIT
                    case 2:
                        $out = $this->getDefaultText('edit', $entity);
                        break;
                    // ADD
                    case 3:
                        $out = $this->getDefaultText('add', $entity);
                        break;
                    // ADD Pergunta
                    case 5:
                        $out = __('Pergunta "<strong>{0}</strong>" no questionário "<strong>{1}</strong>"', $entity->pergunta, $data['extra']['checklistNome']);
                        break;
                    // EDIT Pergunta
                    case 6:
                        $diferente = ($entity->getOriginal('pergunta') != $entity->pergunta);
                        $identificacao = (!$diferente) ? '"<strong>' . $entity->pergunta . '</strong>"' : 'De "<strong>'.$entity->getOriginal('pergunta').'</strong>" para "<strong>'.$entity->pergunta . '</strong>"';
                        $out = __('Pergunta {0} no questionário <strong>{1}</strong>', $identificacao, $data['extra']['checklistNome']);

                        if ($diferente) {
                          $out .= '<p><em>Obs.: Outras informações também podem ter sido alteradas.</p></em>';
                        }
                        break;
                    // DELETE Pergunta
                    case 7:
                        $out = __('Pergunta "<strong>{0}</strong>" no questionário "<strong>{1}</strong>"', $entity->pergunta, $entity->checklist->nome);
                        break;
                }
                break;
        }

        $data['descricao'] = trim($out);

        return $data;
    }

    public function getDefaultText($type, $entity)
    {
        $out = null;

        switch ($type) {
          case 'add':
            $out = '
                <dl>
                  <dt>Nome</dt>
                  <dd>'.$entity->nome.'</dd>
                </dl>
            ';
            break;
          case 'edit';
            $diferente = ($entity->getOriginal('nome') != $entity->nome);
            $identificacao = ($diferente) ? '<dd>De <strong>' .$entity->getOriginal('nome'). '</strong> para <strong>' . $entity->nome .'</strong></dd>' : '<dd>' . $entity->nome .'</dd>';

            $out = '
                <dl>
                  <dt>Criado Por</dt>
                  <dd>'.(($entity->quem_gravou) ? $entity->quem_gravou->short_name : '-').'</dd>
                  <dt>Nome</dt>
                  '.$identificacao.'
                </dl>
                '.(($diferente) ? '<p><em>Obs.: Outras informações também podem ter sido alteradas.</em></p>' : '').'
            ';
            break;
          case 'delete':
            $out = '
                <dl>
                  <dt>Criado Por</dt>
                  <dd>'.(($entity->quem_gravou) ? $entity->quem_gravou->short_name : '-').'</dd>
                  <dt>Nome</dt>
                  <dd>'.$entity->nome.'</dd>
                </dl>
            ';
            break;
        }

        return $out;
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
            ->requirePresence('table_name', 'create')
            ->notEmpty('table_name');

        $validator
            ->integer('ref')
            ->requirePresence('ref', 'create')
            ->notEmpty('ref');
        $validator
            ->integer('autor_id')
            ->requirePresence('autor_id', 'create')
            ->notEmpty('autor_id');

        $validator
            ->dateTime('criado_em');

        $validator
            ->requirePresence('descricao', 'create')
            ->notEmpty('descricao');

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
        $rules->add($rules->existsIn(['logs_tipo_id'], 'LogsTipos'));
        $rules->add($rules->existsIn(['modulo_id'], 'Modulos'));
        $rules->add($rules->existsIn(['grupo_id'], 'Grupos'));

        return $rules;
    }
}
