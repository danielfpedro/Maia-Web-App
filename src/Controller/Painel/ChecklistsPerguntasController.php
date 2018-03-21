<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\BadRequestException;

use Cake\Collection\Collection;

/**
 * ChecklistsPerguntas Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasTable $ChecklistsPerguntas
 */
class ChecklistsPerguntasController extends AppController
{

    public function beforeFilter(Event $event)
    {
        // Todas as perguntas estão ligadas a uma checklist e essa checklist e passada por parametro...
        // aqui verificamos se a checklist passada pode o usuario logado tem autorização
        if ($this->Auth->user() && in_array($this->request->action, ['add', 'reordenar', 'importar'])) {
            if (!$this->ChecklistsPerguntas->Checklists->exists(['id' => (int)$this->request->checklistId, 'grupo_id' => (int)$this->Auth->user('grupo_id')])) {
                throw new NotFoundException();
            }
        }

        if ($this->Auth->user() && in_array($this->request->action, ['edit', 'delete'])) {
            $perguntaId = (int)$this->request->perguntaId;
            if (!$this->ChecklistsPerguntas->possoAcessarPergunta($perguntaId, $this->Auth->user())) {
                throw new NotFoundException();
            }
        }

        $this->Security->config('unlockedActions', ['delete', 'reordenar', 'add', 'edit', 'uploadImagem', 'importar']);

        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Checklists', 'Setores']
        ];
        $checklistsPerguntas = $this->paginate($this->ChecklistsPerguntas);

        $this->set(compact('checklistsPerguntas'));
        $this->set('_serialize', ['checklistsPerguntas']);
    }

    /**
     * View method
     *
     * @param string|null $id Checklists Pergunta id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $checklistsPergunta = $this->ChecklistsPerguntas->get($id, [
            'contain' => ['Checklists', 'Setores', 'Alternativas']
        ]);

        $this->set('checklistsPergunta', $checklistsPergunta);
        $this->set('_serialize', ['checklistsPergunta']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Verifico se tem visita encerrado nesta checklist... caso sim eu
        // já paro por aqui
        $checklist = $this->ChecklistsPerguntas->Checklists->get($this->request->checklistId);
        $total = $this->ChecklistsPerguntas->Checklists->totalVisitasEncerradas($checklist);

        if ($total > 0) {
            throw new BadRequestException("Você não pode adicionar novas perguntas pois este Questionário já possui uma ou mais visitas encerradas.");
        }

        $pergunta = $this->_atualizaPergunta(null, $this->request->checklistId, $this->request->getData('pergunta'));
        $this->set(compact("pergunta"));
        $this->set('_serialize', ["pergunta"]);
    }

    public function edit()
    {
        $pergunta = $this->_atualizaPergunta($this->request->perguntaId, $this->request->checklistId, $this->request->getData('pergunta'));
        $this->set(compact("pergunta"));
        $this->set('_serialize', ["pergunta"]);
    }

    public function _atualizaPergunta($perguntaId, $checklistId, $data)
    {
        // Se possuir 3 imagens e ele deletar todas vai vir sem a key de imagens
        // e não vao ser deletadas... fazendo isso coloco a key vazia
        // e deleta tudo
        if (!isset($data['imagens'])) {
            $data['imagens'] = [];
        }
        // print_r($data);
        // throw new BadRequestException("Error Processing Request", 1);

        $editando = $perguntaId;
        // Recebe o id da checklist passado por parametro, já validei no ::beforeFilter que posso acessar esta checklist
        $data['checklist_id'] = $checklistId;

        if (!in_array($data['tipo'], [1, 2])) {
            throw new BadRequestException("Tipo da pergunta inválido: Permitido(1 e 2), informado (".$data['tipo'].")");
        }
        // Retiro as alternativas se for dissertativa
        // no fluxo normal uma dissertativa nao teria alternativa mas nunca se sabe
        if ((int)$data['tipo'] != 1) {
            unset($data['alternativas']);
        }


        // Se tem visita encerradas não pode alterar algumas coisas
        // Tb pego a checklist para passar o nome dla no log e montar a frase certo
        $checklist = $this->ChecklistsPerguntas->Checklists->get($this->request->checklistId);

        if ($editando) {
            //dd($this->request->getData());
            $pergunta = $this->ChecklistsPerguntas->get($perguntaId, ['contain' => 'Alternativas']);
            $pergunta = $this->ChecklistsPerguntas->patchEntity($pergunta, $data);

            $total = $this->ChecklistsPerguntas->Checklists->totalVisitasEncerradas($checklist);

            if ($total > 0) {
                // Este metodo é void, se houver erro ele joga erro
                $this->ChecklistsPerguntas->validaAlteracaoPerguntaComVisitaEncerrada($pergunta);
            }
        } else {
            // IMPORTANTE!!!
            // Tem que trazer alternativas para validar pq se ele editar form e retirar as
            // alternativas na hora do patch ele ficaria null, trazendo alternativas
            // ele cria o alternativas ai valida legal...
            // 
            $pergunta = $this->ChecklistsPerguntas->newEntity($data, ['associated' => ['Imagens', 'Alternativas']]);
        }

        // Pego todos os setores do ordem
        // Com isso além de ver se o setor da pergunta já está lá ou não, eu tb pego
        // a ordem maior e se o setor não estiver eu salvo ele com a ordem 1 a mais que a ordem maior,
        // ou seja, no final da ordem
        $ordemSetores = $this->ChecklistsPerguntas->Checklists->OrdemSetores->find('all', [
            'conditions' => [
                'checklist_id' => $checklistId
            ]
        ]);

        $temEsteSetor = $ordemSetores->some(function($value) use ($pergunta) {
            return $value->setor_id == $pergunta->setor_id;
        });

        if (!$temEsteSetor) {
            $novo = $this->ChecklistsPerguntas->Checklists->OrdemSetores->newEntity();
            $novo->checklist_id = $pergunta->checklist_id;
            $novo->setor_id = $pergunta->setor_id;
            $novo->ordem = ($ordemSetores->isEmpty()) ? 0 : (($ordemSetores->max('ordem')->ordem) + 1);

            $this->ChecklistsPerguntas->Checklists->OrdemSetores->save($novo);
        }

        if (!$editando) {
            // Pego a maior ordem das perguntas daquela checklist e joga para a nova +1
            $perguntaMaiorOrdem = $this->ChecklistsPerguntas->find('all')->where(['checklist_id' => $checklistId])->max('ordem');
            $pergunta->ordem = ($perguntaMaiorOrdem) ? $perguntaMaiorOrdem->ordem + 1 : 0;
        }

        // Salva a pergunta
        $perguntaAntesSalvar = clone $pergunta;

        $this->ChecklistsPerguntas->saveOrFail($pergunta, ['perguntaAntesSalvar' => $perguntaAntesSalvar]);
        
        //////////////////
        // SALVANDO LOG //
        //////////////////
        $this->loadModel('Logs');

        $dataLog = [
            'modulo_id' => 6,
            'logs_tipo_id' => ($editando) ? 2 : 3,
            'tipo_descricao' => ($editando) ? 6 : 5,
            'table_name' => 'checklists_perguntas',
            'ref' => $pergunta->id,
            'autor_id' => $this->Auth->user('id'),
            'grupo_id' => $this->Auth->user('grupo_id'),
            'extra' => ['checklistNome' => $checklist->nome]
        ];
        $dataLog = $this->Logs->patchData($dataLog, $perguntaAntesSalvar);
        $log = $this->Logs->newEntity($dataLog);
        $this->Logs->saveOrFail($log);            
        //////////////////////
        // FIM SALVANDO LOG //
        //////////////////////

        // Se a pergunta é tipo == 2 a gente deleta as alternativas que ela possa ter
        // para não bagunçar quando for pegar alguns dados
        // if ($pergunta->tipo == 2) {
        //     if (isset($pergunta->alternativas)) {
        //         foreach ($pergunta->alternativas as $alternativa) {
        //             $this->ChecklistsPerguntas->Alternativas->deleteOrFail($alternativa);
        //         }
        //     }
        // }

        /**
         * Importante!
         *
         * Caso um setor só tenha um pergunta e a gente mova essa pergunta de setor, o setor
         * vai ficar vazio, então temos que um em OrdemSetores e deletar este setor vazio
         */
        //Pego todos os setores das perguntas
        $todosSetoresDasPerguntas = $this->ChecklistsPerguntas
            ->find('all')
            ->select([
                'setor_id'
            ])
            ->where([
                'checklist_id' => $checklistId
            ])
            ->extract('setor_id');

        $todosSetoresDasPerguntas = array_unique($todosSetoresDasPerguntas->toArray());
        foreach ($ordemSetores as $k => $v) {
            if (!in_array($v->setor_id, $todosSetoresDasPerguntas)) {
                $this->ChecklistsPerguntas->Checklists->OrdemSetores->delete($v);
            }
        }

        return $pergunta;
    }

    /**
     * Pega os id das perguntas e salva na checklist, no beforeFilter já estou validadno o id da checklist
     * que vai salvar
     * Não esquecer de validar se as perguntas são do grupo dele
     */
    public function importar()
    {
        $totalVisitasEncerradas = $this->ChecklistsPerguntas->Checklists->totalVisitasEncerradas($this->ChecklistsPerguntas->Checklists->get($this->request->checklistId));

        if ($totalVisitasEncerradas > 0) {
            throw new BadRequestException('Esta Checklists não pode receber novas perguuntas.');
        }

        if (count($this->request->getData('importar_perguntas')) > 0) {
            // Pego as respostas das perguntas passadas e garanto que elas sao de uma
            // checklists que é de meu grupo
            $perguntas = $this->ChecklistsPerguntas->find()
                ->where(['ChecklistsPerguntas.id IN' => $this->request->getData('importar_perguntas')])
                ->contain([
                    'Alternativas',
                    'Imagens',
                    'Checklists'
                ]);

            foreach ($perguntas as $pergunta) {
                if ($pergunta->checklist->grupo_id == $this->Auth->user('grupo_id')) {
                    // Retiro o id atual da pergiunta pois não estamos editando ela e sim
                    // pegando a mesma como prototipo para salvar uma nova na checklist que está importando
                    $perguntaArray = clone $pergunta;
                    $perguntaArray = $perguntaArray->toArray();
                    unset($perguntaArray['id']);
                    // Importante tirar, a checklist estava ai só pra gente validar o grupo
                    unset($perguntaArray['checklist']);
                    $perguntaArray['checklist_id'] = $this->request->checklistId;

                    if (isset($perguntaArray['alternativas']) && is_array($perguntaArray['alternativas'])) {
                        foreach ($perguntaArray['alternativas'] as $key => $alternativa) {
                            $perguntaArray['alternativas'][$key]['id'] = null;
                        }
                    }

                    $this->_atualizaPergunta(null, $this->request->checklistId, $perguntaArray);
                    
                }
            }
        }
    }

    /**
     * Já validei se posso acesar a pergunta no ::beforeFilter então só deleto normal
     */
    public function delete()
    {
        // Deve conter alternativas
        $pergunta = $this->ChecklistsPerguntas->get($this->request->perguntaId, ['contain' => ['Checklists', 'Alternativas']]);

        if ($this->ChecklistsPerguntas->Checklists->totalVisitasEncerradas($pergunta->checklist) > 0) {
            throw new BadRequestException("A pergunta não pode ser deletada pois existem uma ou mais visitas encerradas ligadas a esta Checklist.");
        }

        $pergunta->set('deletado', true);

        $this->ChecklistsPerguntas->saveOrFail($pergunta);

                //////////////////
        // SALVANDO LOG //
        //////////////////
        $this->loadModel('Logs');

        $dataLog = [
            'modulo_id' => 6,
            'logs_tipo_id' => 1,
            'tipo_descricao' => 7,
            'table_name' => 'checklists_perguntas',
            'ref' => $pergunta->id,
            'autor_id' => $this->Auth->user('id'),
            'grupo_id' => $this->Auth->user('grupo_id')
        ];
        $dataLog = $this->Logs->patchData($dataLog, $pergunta);
        $log = $this->Logs->newEntity($dataLog);
        $this->Logs->saveOrFail($log);            
        //////////////////////
        // FIM SALVANDO LOG //
        //////////////////////

        // IMPORTANTÍSSIMO
        //
        // Deletar todos os ORDEM SETORES que não possuirem pergunta
        // pois a gente move de um setor e ele pode ficar vazio... ai a gente deleta
        $this->ChecklistsPerguntas->Checklists->OrdemSetores->deletaSemPerguntas($pergunta->checklist->id);

        $response = ['ok'];

        $this->set(compact('response'));
        $this->set('_serialize', []);
    }

    public function reordenar()
    {

        $validarTrocaDeSetor = false;

        $checklist = $this->ChecklistsPerguntas->Checklists->get($this->request->checklistId);

        if ($this->ChecklistsPerguntas->Checklists->totalVisitasEncerradas($checklist) > 0) {
            $validarTrocaDeSetor = true;
        }

        $i = 0;
        foreach ($this->request->data['perguntas'] as $pergunta) {

            $perguntaEntity = $this->ChecklistsPerguntas->get((int)$pergunta['id'], [
                'contain' => [
                    // IMPORTANTE: DEVE CONTER ALTERNATIVAS!!!!!!!!!!!!!!!
                    'Alternativas',
                    'Checklists' => function($query) {
                        return $query->select(['Checklists.id', 'Checklists.grupo_id']);
                    }
                ]
            ]);

            if ($validarTrocaDeSetor && $pergunta['setor_id'] != $perguntaEntity->setor_id) {
                throw new BadRequestException("Você ordenou a pergunta para um setor diferente porém a Checklist já possui uma visita encerrada e não pode mais sofrer alterações desse tipo.");
            }

            // Pego o novo setor da pergunta (pode não ter mudado mas dane-se)
            $setor = $this->ChecklistsPerguntas->Setores->get($pergunta['setor_id']);

            if ($perguntaEntity->checklist->grupo_id != $this->Auth->user('grupo_id')) {
                throw new BadRequestException('Pergunta não encontrada');
            }
            if ($setor->grupo_id != $this->Auth->user('grupo_id')) {
                throw new BadRequestException('Pergunta não encontrada');
            }

            $perguntaEntity = $this->ChecklistsPerguntas->patchEntity($perguntaEntity, ['setor_id' => $pergunta['setor_id'], 'ordem' => $i]);
            $this->ChecklistsPerguntas->saveOrFail($perguntaEntity);
            // try {
            //     $this->ChecklistsPerguntas->saveOrFail($perguntaEntity);
            // } catch (\Exception $e) {
            //     echo json_encode($perguntaEntity->errors(), JSON_PRETTY_PRINT);
            //     throw new \Exception("Error Processing Request", 1);
            // }

            $i++;
        }

        // IMPORTANTÍSSIMO
        //
        // Deletar todos os ORDEM SETORES que não possuirem pergunta
        // pois a gente move de um setor e ele pode ficar vazio... ai a gente deleta
        $this->ChecklistsPerguntas->Checklists->OrdemSetores->deletaSemPerguntas($this->request->checklistId);

        $response = ['message' => 'ok', 'code' => 200];

        $this->set(compact('response'));
        $this->set('_serialize', []);
    }

    public function todasPorChecklist()
    {
        $checklist = $this->ChecklistsPerguntas->Checklists->todosVivosDoMeuGrupo('all', $this->Auth->user())
            ->where(['Checklists.id' => $this->request->query('value')])
            ->first();

        if (!$checklist) {
            throw new NotFoundException();
        }

        $perguntas = $this->ChecklistsPerguntas->find('all')
            ->select([
                'ChecklistsPerguntas.id',
                'text' => 'ChecklistsPerguntas.pergunta',
                'ChecklistsPerguntas.setor_id'
            ])
            ->where([
                'ChecklistsPerguntas.checklist_id' => $checklist->id,
                'ChecklistsPerguntas.deletado' => false
            ])
            ->order(['ChecklistsPerguntas.ordem'])
            ->contain(['Setores' => function($query) {
                return $query
                    ->select([
                        'Setores.id',
                        'Setores.nome'
                    ]);
            }]);

        $setoresCollection = $perguntas->extract('setor')->compile()->indexBy('id');
        $perguntas = $perguntas->groupBy('setor_id')->toArray();

        foreach($setoresCollection as $setor) {
            $setor->text = $setor->nome;
            $setor->children = [];

            if (isset($perguntas[$setor->id])) {
                foreach ($perguntas[$setor->id] as $keyPergunta => $pergunta) {
                    $perguntas[$setor->id][$keyPergunta]['text'] = '['.$setor->nome.'] ' . $perguntas[$setor->id][$keyPergunta]['text'];
                }
                $setor->children = $perguntas[$setor->id];
            }
        }

        $setores = [];
        foreach ($setoresCollection as $setor) {
            $setores[] = $setor;
        }

        $setores = (new Collection($setores))->sortBy('nome')->toArray();
        // dd($setores);

        $this->set(compact('setores'));
        $this->set('_serialize', 'setores');   
    }

}
