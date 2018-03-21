<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Event\Event;
use Cake\Collection\Collection;

use Cake\Mailer\MailerAwareTrait;

// Exception
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Checklists Controller
 *
 * @property \App\Model\Table\ChecklistsTable $Checklists
 */
class ChecklistsController extends AppController
{

    // Traits
    use MailerAwareTrait;

    public function beforeFilter(Event $event) {
        /**
         * Aqui eu especifico todas as actions que eu passo o checklist como parametro e validamos e
         * o id do checklist passado pertence ao usuário logado, caso contrario já
         * lança um NotFoundException e já mata ali mesmo
         */
        $actionsParaValidar = [
            'edit',
            'delete',
            'perguntasForm',
            'perguntas',
            'autocompleteImportar',
            'inativarVisitas',
            'getSetores'
        ];

        if ($this->Auth->user() && in_array($this->request->action, $actionsParaValidar)) {
            if (!$this->Checklists->exists([
                'id' => (int)$this->request->checklistId,
                'grupo_id' => (int)$this->Auth->user('grupo_id'),
                'deletado' => false
            ])) {
                throw new NotFoundException();
            }
        }

        /**
         * Como edit e add não usam o template padrão e sim um diferente e igual
         * entre os dois eu faço isso aqui
         */
        if (in_array($this->request->action, ['edit', 'add'])) {
            $this->viewBuilder()->template('form');
        }

        $this->Security->config('unlockedActions', ['criarDoModelo']);
        parent::beforeFilter($event);

    }
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('Checklists.index', ['controller' => 'Checklists', 'action' => 'index']);

        $finder = $this->Checklists->find();

        $subquery = $this->Checklists->Visitas->find()
          ->select(['id'])
          ->where([
              'Visitas.checklist_id = Checklists.id'
          ]);

        $finder
            ->find('doMeuGrupo', $this->Auth->user())
            ->find('vivos', $this->Auth->user())
            ->find('dosMeusGruposDeAcessos', $this->Auth->user())
            ->select([
                'Checklists.id',
                'Checklists.nome',
                'Checklists.ativo',
                'Checklists.sem_agendamento_flag',
                'Checklists.minimo_esperado',
                'Checklists.criado_em',
                'total_perguntas' => 'COUNT(DISTINCT Perguntas.id)',
                'total_visitas_encerradas' => 'COUNT(DISTINCT Visitas.id)',
                'QuemGravou.nome',
                'Segmentos.nome'
            ])
            ->leftJoinWith('QuemGravou')
            ->leftJoinWith('Perguntas')
            ->leftJoinWith('Visitas')
            ->leftJoinWith('Segmentos')
            ->leftJoinWith('Visitas', function($query) {
                return $query
                    ->where([
                        'Visitas.dt_encerramento IS NOT' => null,
                        'Visitas.deletado' => false,
                    ]);
            })
            ->contain([
                'GruposDeAcessos'
            ])
            ->group([
                'Checklists.id',
                'Checklists.nome',
                'Checklists.ativo',
                'Checklists.minimo_esperado',
                'Checklists.criado_em',
                'QuemGravou.nome'
            ])
            ->order(['Checklists.ativo' => 'DESC', 'Checklists.criado_em' => 'DESC', 'Checklists.nome' => 'DESC']);

        if ($this->request->query('q')) {
            $q = '%' . str_replace('%', ' ', $this->request->query('q')) . '%';
            $finder->where(['Checklists.nome LIKE' => $q]);
        }

        if ($this->request->query('status')) {
            $status = ($this->request->query('status') == 1) ? 1 : 0;
            $finder->where(['Checklists.ativo' => $status]);
        }

        $checklists = $this->paginate($finder);

        $this->set(compact('checklists'));
    }


    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Checklists.index', ['action' => 'index']);

        $checklist = $this->Checklists->newEntity();

        if ($this->request->is('post')) {

            $this->request->data['grupo_id'] = $this->Auth->user('grupo_id');

            $checklist = $this->Checklists->patchEntity($checklist, $this->request->data);

            // dd($checklist);

            if ($this->Checklists->save($checklist)) {

                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $checklistParaLog = $this->Checklists->get($checklist->id, ['contain' => ['QuemGravou']]);

                $dataLog = [
                    'modulo_id' => 6,
                    'logs_tipo_id' => 3,
                    'tipo_descricao' => 3,
                    'table_name' => 'checklists',
                    'ref' => $checklist->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $checklistParaLog);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Questionário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect(['action' => 'perguntasForm', 'checklistId' => $checklist->id]);
            }
            $this->Flash->set('O Questionário não foi salvo.', ['element' => 'Painel/error']);
        }

        $segmentos = $this->Checklists->Segmentos->todosAtivos('list');
        $gruposDeAcessos = $this->Checklists->GruposDeAcessos->todosDoMeuGrupo('list', $this->Auth->user());

        $this->set(compact('checklist', 'segmentos', 'gruposDeAcessos', 'breadcrumb'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Checklists.index', ['action' => 'index']);

        $checklist = $this->Checklists->get($this->request->checklistId, ['contain' => ['QuemGravou', 'GruposDeAcessos']]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $this->request->data['grupo_id'] = $this->Auth->user('grupo_id');

            $checklist = $this->Checklists->patchEntity($checklist, $this->request->data);

            $checklistAntesSalvar = clone $checklist;
            if ($this->Checklists->save($checklist)) {

                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $dataLog = [
                    'modulo_id' => 6,
                    'logs_tipo_id' => 2,
                    'tipo_descricao' => 2,
                    'table_name' => 'checklists',
                    'ref' => $checklist->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $checklistAntesSalvar);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Questionário foi salva.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('Checklists.index', ['action' => 'index']));
            }
            $this->Flash->set('O Questionário não foi salva. Por favor, tente novamente.', ['element' => 'Painel/error']);
        }

        $segmentos = $this->Checklists->Segmentos->todosAtivos('list');
        $gruposDeAcessos = $this->Checklists->GruposDeAcessos->todosDoMeuGrupo('list', $this->Auth->user());

        $this->set(compact('checklist', 'segmentos', 'gruposDeAcessos', 'breadcrumb'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);
        $checklist = $this->Checklists->get($this->request->checklistId, ['contain' => ['QuemGravou']]);

        $checklist->deletado = true;

        $totalVisitasEncerradasDestaChecklist = $this->Checklists->Visitas->todosVivosDoMeuGrupo('all', $this->Auth->user())
            ->where([
                'Visitas.checklist_id' => $checklist->id,
                'Visitas.dt_encerramento IS NOT' => null
            ])
            ->count();

        if ($this->Checklists->totalVisitasEncerradas($checklist) > 0) {
            throw new BadRequestException("O Questionário não pode ser removido pois uma ou mais visitas ligadas a ela já foram encerradas.");
        }

        if ($this->Checklists->save($checklist)) {
            //////////////////
            // SALVANDO LOG //
            //////////////////
            $this->loadModel('Logs');

            $dataLog = [
                'modulo_id' => 6,
                'logs_tipo_id' => 1,
                'tipo_descricao' => 1,
                'table_name' => 'checklists',
                'ref' => $checklist->id,
                'autor_id' => $this->Auth->user('id'),
                'grupo_id' => $this->Auth->user('grupo_id'),
            ];
            $dataLog = $this->Logs->patchData($dataLog, $checklist);
            $log = $this->Logs->newEntity($dataLog);
            $this->Logs->saveOrFail($log);            
            //////////////////
            // FIM SALVANDO LOG //
            //////////////////

            $this->Flash->set(__('O Questionário foi deletado.'), ['element' => 'Painel/success']);
        } else {
            $this->Flash->set(__('O Questionário não foi deletado.'), ['element' => 'Painel/error']);
        }

        return $this->redirect($this->breadcrumbRedirect('Checklists.index', ['action' => 'index']));
    }

    /**
     * Perguntas, no before filter eu já validei se posso entrar aqui pelo checklist
     * passado, ou seja, aqui dentro posso trabalhar seguro com o checklist passado
     * pois já verifiquei a integridade.
     */
    public function perguntasForm()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Checklists.index', ['action' => 'index']);   

        // Leio o Alternativas pq ele não está relacionado diretamente com nenhum aqui
        // isso é bom
        $this->loadModel('ModelosAlternativas');

        /**
         * Pego id e nome da checklist para usar na página.
         */
        $checklist = $this
            ->Checklists
            ->get($this->request->checklistId, [
                'fields' => ['id', 'nome', 'grupo_id'],
                // 'contain' => [
                //     'OrdemSetores.Setores'
                // ]
            ]);

        /**
         * Pego os setores para popular na combobox.
         */
        $setores = $this->Checklists->Perguntas->Setores
            ->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
            ->order(['Setores.nome']);

        /**
         * Pego os modelos de alternativas com as alternativas para popular a combobox
         */
        $modelosAlternativas = $this->ModelosAlternativas->todosAtivosDoMeuGrupo('all', $this->Auth->user())
            ->select([
                'ModelosAlternativas.id',
                'ModelosAlternativas.nome',
            ])
            ->contain([
                'AlternativasDosModelos' => function($query) {
                    return $query->select([
                        'AlternativasDosModelos.id',
                        'AlternativasDosModelos.alternativa',
                        'AlternativasDosModelos.valor',
                        'AlternativasDosModelos.tem_foto',
                        'AlternativasDosModelos.item_critico',
                        'AlternativasDosModelos.modelos_alternativa_id',
                    ])
                    ->order(['AlternativasDosModelos.ordem']);
                }
            ]);

        $totalVisitasEncerradas = $this->Checklists->totalVisitasEncerradas($checklist);

        $this->set(compact(
            'checklist',
            'setores',
            'modelosAlternativas',
            'totalVisitasEncerradas',
            'breadcrumb'
        ));
    }

    /**
     * Carrego as pergunas da checklist por ajax, posso trabalhar seguro com o
     * checklistId passado pois já validei no ::beforeFilter
     */
    public function perguntas()
    {
        $checklistId = (int)$this->request->checklistId;

        /**
         * Pego todas as perguntas desse checklist com suas respectivas alternativas
         */
        $query = $this->Checklists->Perguntas->find('all');
        $perguntas = $query
            ->select([
                'Perguntas.id',
                'Perguntas.pergunta',
                'Perguntas.setor_id',
                'Perguntas.tipo',
            ])
            ->contain([
                'Alternativas' => function($query) {
                    return $query
                        ->select([
                            'Alternativas.id',
                            'Alternativas.alternativa',
                            'Alternativas.valor',
                            'Alternativas.tem_foto',
                            'Alternativas.item_critico',
                            'Alternativas.checklists_pergunta_id'
                        ])
                        ->order(['Alternativas.ordem' => 'ASC']);
                },
                'Imagens' => function($query) {
                    return $query
                        ->select([
                            'Imagens.id',
                            'Imagens.legenda',
                            'Imagens.nome_arquivo',
                            'Imagens.checklists_pergunta_id',
                            'Imagens.folder'
                        ])
                        ->order(['ordem' => 'ASC']);
                },
            ])
            ->where([
                'Perguntas.deletado' => false,
                'Perguntas.checklist_id' => $checklistId
            ])
            ->order(['Perguntas.ordem']);

        // dd($perguntas->toArray());

        /**
         * Da própria coleção de perguntas eu extraio os ids dos setores para na hora
         * de pegar os setores eu só pego dos que tem perguntas
         */
        $setoresDasPerguntas = $perguntas->extract('setor_id')->toArray();
        // dd($setoresDasPerguntas);

        /**
         * Aqui eu crio o array vazi o das perguntas ordenadas por setor pois se não houver nenhum já tenho um array vazio
         * @var array
         */
        $perguntasPorSetorOrdenado = [];

        if (!$perguntas->isEmpty()) {

            /**
             * Pego os setores ordenados e faço um contain com setores para pegar
             * seus nomes e montar o array $perguntasPorSetorOrdenado mais abaixo
             */
            $setoresOrdenadosRaw = $this->Checklists->OrdemSetores->find('all')
                ->select([
                    'OrdemSetores.id',
                    'OrdemSetores.setor_id',
                    'OrdemSetores.ordem'
                ])
                ->contain([
                    'Setores' => function($query) {
                        return $query
                            ->select([
                                'Setores.id',
                                'Setores.nome'
                            ]);
                    }
                ])
                ->where([
                    'OrdemSetores.setor_id IN' => $setoresDasPerguntas,
                    'OrdemSetores.checklist_id' => $checklistId
                ])
                ->order('ordem');

            /**
             * Aqui crio um array com os setores certinho e com a index setor_id
             * para depois jogar as perguntas para os setores certinho
             */
            foreach ($setoresOrdenadosRaw as $key => $value) {
                $perguntasPorSetorOrdenado[$value['setor_id']]['id'] = $value->setor->id;
                $perguntasPorSetorOrdenado[$value['setor_id']]['nome'] = $value->setor->nome;
                $perguntasPorSetorOrdenado[$value['setor_id']]['ordem'] = $value->ordem;
            }
            // dd($perguntasPorSetorOrdenado);

            /**
             * Aqui faço um loop um vez pelos perguntas eu vou jogando elas no
             * setor certinho
             */
            foreach ($perguntas as $key => $pergunta) {
                if (isset($perguntasPorSetorOrdenado[$pergunta['setor_id']])) {
                    /**
                     * Coloco um array setor dentro da pergunta pq o javascript usa isso
                     */
                    $pergunta->setor = (object)[];
                    $pergunta->setor->id = $perguntasPorSetorOrdenado[$pergunta['setor_id']]['id'];
                    $pergunta->setor->nome = $perguntasPorSetorOrdenado[$pergunta['setor_id']]['nome'];

                    $perguntasPorSetorOrdenado[$pergunta['setor_id']]['perguntas'][] = $pergunta;
                }
            }
            // dd($perguntasPorSetorOrdenado);

            /**
             * Como a index dos setores é o id deles agora eu reseto para que as
             * index fiquem (0, 1, 2...) pois quando a gente converte para json ele
             * reordena de acordo com o index entao estava embaralhando  a ordem correta
             * @var array
             */
            $perguntasPorSetorOrdenado = array_values($perguntasPorSetorOrdenado);
            // dd($perguntasPorSetorOrdenado);
        }

        $this->set(compact('perguntasPorSetorOrdenado'));
        $this->set('_serialize', ['perguntasPorSetorOrdenado']);
    }

    public function autocompleteImportar()
    {
        // Pode usar this->request->checklistId pq já está valindando no beforeFilter
        $setores = $this->Checklists->OrdemSetores->find()
            ->where([
                'OrdemSetores.checklist_id' => $this->request->checklistId
            ])
            ->contain(['Setores']);

        // Pego a checklist do meu grupo, ativa e de acordo com o termo
        // Pego com as perguntas pq se ele aceitar elas já estão lá... lembrando que
        // só as perguntas.. alternativas nao.
        // A checklist tb deve ser diferente da checklist atual que ele está claro
        $q = '%' . trim(str_replace(' ', '%', $this->request->getQuery('term'))) . '%';

        $checklists = $this->Checklists
            ->find()
            ->select(['Checklists.id', 'label' => 'Checklists.nome'])
            ->contain([
                'Perguntas',
                'OrdemSetores' => function($query) {
                    return $query
                        ->contain('Setores')
                        ->order(['OrdemSetores.ordem']);
                }
            ])
            ->where([
                'Checklists.grupo_id' => $this->Auth->user('grupo_id'),
                'Checklists.id !=' => $this->request->checklistId,
                'Checklists.nome LIKE' => $q,
                'Checklists.deletado' => false
            ])
            ->order(['Checklists.nome'])
            ->limit(15);

        // Eu geralmente nao precisaria faer isso mas como vou passar o json dados e nao a entidade
        // eu tenho que dazer
        foreach ($checklists as $checklist) {
            if (count($checklist->perguntas) < 1) {
                $checklist->label .= ' (Sem perguntas)';
            }
            $checklist->perguntas_por_setores_ordenados = $checklist->getPerguntasPorSetoresOrdenados();
        }

        $this->set(compact('checklists'));
        $this->set('_serialize', 'checklists');
    }

    /**
     * Pode usar o checklistsId tranquilo pois já estopu validando ele no beforeFilter
     */
    public function inativarVisitas()
    {
        // Somente Visitas vivas, ativas e NÃO ENCERRADAS
        $visitas = $this->Checklists->Visitas->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
            ->where([
                'Visitas.checklist_id' => $this->request->checklistId,
                'OR' => [
                    'Visitas.dt_encerramento IS' => null,
                    'Visitas.dt_encerramento' => '',
                ]
            ]);

        foreach ($visitas as $visita) {
            $visita->set('ativo', false);
            $this->Checklists->Visitas->save($visita);

            //Manda email
            // Pode mandar email pra todos pq a gente selecionou só as visitas
            // que já estavam ativas então não vai acontecer de mandar email para visitas
            // que já estava inativa e já tinha recebido o email de cancelada
            $visita = $this->Checklists->Visitas->get($visita->id, ['contain' => ['Usuarios', 'Lojas.Cidades']]);
            $quemCancelou = $this->Checklists->Visitas->Usuarios->get($this->Auth->user('id'), ['contain' => 'Grupos']);

            $this->getMailer('Visitas')->send('cancelada', [$visita, $quemCancelou]);
        }

        $response = [
            'totalAfetadas' => count($visitas->toArray())
        ];

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    // No importar checklists preciso carregar as perguntas dentro dos setores
    // para mostrar para ele do que se trata o questionario e tb
    // ele pode escolher la não importar algumas perguntas
    public function carregaPerguntas()
    {
        $checklist = $this->Checklists->getModelo($this->request->checklistId)
            ->contain(['Perguntas.Alternativas', 'OrdemSetores.Setores'])
            ->first();

        if (!$checklist) {
            throw new NotFoundException();
        }

        $checklist->perguntas_por_setores = $checklist->getPerguntasPorSetoresOrdenados();

        $this->set(compact('checklist'));
        $this->set('_serialize', 'checklist');

    }

    public function criarDoModelo()
    {
        $response = ['oi'];

        $checklistModelo = $this->Checklists->getModelo($this->request->checklistId)
            ->contain([
                'Perguntas' => function($query) {
                    return $query
                        ->contain([
                            'Alternativas',
                            'Imagens'
                        ]);
                },
                'OrdemSetores.Setores'
            ])
            ->first();

        if (!$checklistModelo) {
            throw new NotFoundException();
        }        

        // Deixo apenas as perguntas que ele selecionou no form que ele
        if ($this->request->getData('importar_perguntas')) {
            $checklistModelo->perguntas = (new Collection($checklistModelo->perguntas))->reject(function($pergunta) {
                return !in_array($pergunta->id, $this->request->getData('importar_perguntas'));
            })
            ->toArray();
        }

        // Vejo se restou ao menos uma
        $response = $checklistModelo->perguntas;
        if (count($checklistModelo->perguntas) < 1 || !$this->request->getData('importar_perguntas')) {
            throw new BadRequestException("Você deve selecionar ao menos uma pergunta.");
        }

        $checklistModelo->id = null;
        $checklistModelo->segmento_id = null;
        $checklistModelo->grupo_id = $this->Auth->user('grupo_id');

        // NOME DO QUESTIONARIO
        $checklistModelo->nome = $this->request->getData('nome_questionario');
        // $checklistModelo->ordem_setores = null;

        foreach ($checklistModelo->perguntas as $pergunta) {
            $pergunta->id = null;
            $pergunta->checklist_id = null;
        }

        // Pego todos os setores vivos do Grupo
        $meusSetores = $this->Checklists->OrdemSetores->Setores
            ->todosVivosDoMeuGrupo('all', $this->Auth->user());

        foreach ($checklistModelo->ordem_setores as $ordemSetor) {

            $itsAMatch = [];
            foreach ($meusSetores as $setor) {
                if (strtolower($setor->nome) == strtolower($ordemSetor->setor->nome)) {
                    $itsAMatch = $setor;

                    $setor->id_antigo = $ordemSetor->setor->id;
                    break;
                }
            }


            if (!$itsAMatch) {

                $data = ['nome' => $ordemSetor->setor->nome];
                $setorAdd = $this->Checklists->OrdemSetores->Setores->newEntity();

                $setorAdd = $this->Checklists->OrdemSetores->Setores->patchEntity($setorAdd, $data, ['entity' => $setorAdd, 'userData' => $this->Auth->user()]);
                $setorAdd->set('ativo', true);

                try {
                    $this->Checklists->OrdemSetores->Setores->saveOrFail($setorAdd);
                } catch (\Exception $e) {
                    debug($setorAdd->errors());
                    throw new \Exception("Error Processing Request");
                    
                }

                // Insetir novo setor na variavel meus setores para setar as perguntas
                // e com seus respectivos ids antigos.

                $ordemSetor->id_antigo = $ordemSetor->setor_id;
                $ordemSetor->setor_id = $setorAdd->id;
            } else {
                $ordemSetor->id_antigo = $ordemSetor->setor_id;
                $ordemSetor->setor_id = $itsAMatch->id;
            }

            unset($ordemSetor->id);
            unset($ordemSetor->checklist_id);
            unset($ordemSetor->setor);
        }

        foreach ($checklistModelo->perguntas as $pergunta) {
            unset($pergunta->id);
            unset($pergunta->checklist_id);

            foreach ($checklistModelo->ordem_setores as $ordemSetor) {
                if ($pergunta->setor_id == $ordemSetor->id_antigo) {
                    $pergunta->setor_id = $ordemSetor->setor_id;
                }
            }

            foreach ($pergunta->alternativas as $alternativa) {
                unset($alternativa->id);
                unset($alternativa->checklists_pergunta_id);
            }

            foreach ($pergunta->imagens as $imagem) {
                unset($imagem->id);
                unset($imagem->checklists_pergunta_id);
            }
        }

        $checklist = $this->Checklists->newEntity($checklistModelo->toArray(), ['associated' => ['OrdemSetores', 'Perguntas.Alternativas', 'Perguntas.Imagens']]);

        $checklistAntesSalvar = clone $checklist;

        if (!$this->Checklists->save($checklist)) {
            if ($checklist->errors('nome')) {
                throw new ForbiddenException(join($checklist->errors('nome'), '<br>'));
            } else {
                throw new BadRequestException();
            }
        }


        //////////////////
        // SALVANDO LOG //
        //////////////////
        $this->loadModel('Logs');

        $dataLog = [
            'modulo_id' => 6,
            'logs_tipo_id' => 3,
            'tipo_descricao' => 3,
            'table_name' => 'checklists',
            'ref' => $checklist->id,
            'autor_id' => $this->Auth->user('id'),
            'grupo_id' => $this->Auth->user('grupo_id'),
        ];
        $dataLog = $this->Logs->patchData($dataLog, $checklistAntesSalvar);
        $log = $this->Logs->newEntity($dataLog);
        try {
            $this->Logs->saveOrFail($log);
        } catch (\Exception $e) {
            echo json_encode($log->errors(), JSON_PRETTY_PRINT);
            exit();
        }
        //////////////////
        // FIM SALVANDO LOG //
        //////////////////
        // if (!$this->Checklists->save($checklist)) {
        //     dd($checklist->errors());
        // }

        // copio as imagens
        foreach ($checklist->perguntas as $pergunta) {
            foreach ($pergunta->imagens as $imagem) {
                $file = new File(WWW_ROOT  . $imagem->folder . $imagem->nome_arquivo);
                $fileThumb = new File(WWW_ROOT  . $imagem->folder . 'quadrada_' . $imagem->nome_arquivo);
                // Crio folder destino

                $folderDestinoParts = [
                    '',
                    'files',
                    'grupos',
                    $this->Auth->user('grupo_id'),
                    'checklists',
                    'imagens_referencias',
                    $checklist->id,
                    ''
                ];

                $folderDestino = new Folder(WWW_ROOT  . trim(join($folderDestinoParts, DS)), true);

                $file->copy($folderDestino->path . $imagem->nome_arquivo);
                $fileThumb->copy($folderDestino->path . 'quadrada_' . $imagem->nome_arquivo);

                $imagem->folder = trim(join($folderDestinoParts, '/'));

                try {
                    $this->Checklists->Perguntas->Imagens->saveOrFail($imagem);
                } catch (\Exception $e) {
                    echo json_encode($imagem->errors(), JSON_PRETTY_PRINT);
                    exit();
                }
            }
        }

        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    public function carregaModelos()
    {
        $usuario = $this->Checklists->Grupos->Usuarios->get($this->Auth->user('id'), ['contain' => ['Grupos']]);

        $checklists = $this->Checklists->find()
            ->select([
                'Checklists.id',
                'Checklists.nome',
            ])
            ->where([
                // Somente do grupo modelo de questionarios que existe para isso
                // mesmo
                'Checklists.segmento_id' => (int)$usuario->grupo->segmento_id,
                'Checklists.grupo_id' => 1,
                'Checklists.deletado' => false
            ])
            ->order(['Checklists.nome']);

        $this->set(compact('checklists'));
        $this->set('_serialize', 'checklists');
    }

    public function getSetoresParaCombo()
    {
        $setores = $this->Checklists->OrdemSetores->find()
            ->where([
                'OrdemSetores.checklist_id' => (int)$this->request->checklistId
            ])
            ->contain(['Setores' => function($query) {
                return $query->select(['Setores.id', 'Setores.nome']);
            }]);

        $setores = $setores->extract('setor')->each(function($setor) {
            $setor->text = $setor->nome;
            return $setor;
        });

        $this->set(compact('setores'));
        $this->set('_serialize', 'setores');

    }

    public function getPerguntasParaCombo()
    {
        $perguntas = $this->Checklists->Perguntas->find()
            ->select(['Perguntas.id', 'Perguntas.pergunta', 'Perguntas.setor_id'])
            ->contain([
                'Setores' => function($query) {
                    return $query->select(['Setores.id', 'Setores.nome']);
                }
            ])
            ->where(['Perguntas.checklist_id' => (int)$this->request->checklistId]);

        $setores = $perguntas->extract('setor') // Extraio os setores da perguntas
            ->indexBy('id')                     // indexa pelo id entao os setores repetidos se tornam um só
            ->toArray();                        // Transformo para array explico abaixo

        // Aqui eu uso array_values pq quando eu indexo acima ele vira um coleção de objectos e não um array de objetos
        // depois transform em ::Collection para usar um each mais fancy mais para baixo
        $setores = new Collection(array_values($setores));

        // Perguntas serão appendiadas aos setores mais para baixo entao aqui eu tiro o setor
        // delas para não ficar uma bagunça
        // O extract acima não retiro de $perguntas, ele copiou para o $setores
        $perguntas->each(function($pergunta){
            unset($pergunta->setor);
            return $pergunta;
        });

        // Agrupo as perguntas pelo setor_id então depois para jogar elas para os seus setores
        // eu pego pela index
        $perguntasGroupedBySetorId = $perguntas->groupBy('setor_id')->toArray();

        $setores->each(function($setor) use ($perguntasGroupedBySetorId) {
            // Para o select 2 entender
            $setor->text = $setor->nome;

            // Insiro as perguntas na key 'children' do setor
            $children = (isset($perguntasGroupedBySetorId[$setor->id])) ? $perguntasGroupedBySetorId[$setor->id] : [];
            $setor->children = new Collection($children);

            // Para o select2 entender
            $setor->children->each(function($child) {
                $child->text = $child->pergunta;
                return $child;
            });

            return $setor;
        });

        $this->set(compact('setores'));
        $this->set('_serialize', 'setores');

    }

}
;