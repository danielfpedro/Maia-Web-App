<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;

use Cake\Event\Event;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\BadRequestException;
use Cake\Collection\Collection;
// Email
use Cake\Mailer\MailerAwareTrait;

/**
 * Usuarios Controller
 *
 * @property \App\Model\Table\UsuariosTable $Usuarios
 */
class UsersController extends AppController
{
    /**
     * Eu chamo de form add/edit actions, aqui eu falo quais os campos que serão acessados caso
     * algum campo seja injetado no form, tem o component security do cake que não permite injeção
     * de campo mas nunca se sabe rsrs.
     *
     * posso criar mais variaveis dessas se eu possuir actions que exijam campos diferentes
     */
    protected $_formAllowedFields = [
        'nome',
        'email',
        'senha',
        'cargos',
        // Não esquecer de injetar manualmnete, ele precisar estar no data pois a
        // validação é feita no patch e algumas regras usam o grupo_id
        'grupo_id',
        'lojas',
        'ativo'
    ];

    // Traits
    use MailerAwareTrait;

    public function beforeFilter(Event $event)
    {
        /**
         * No edit e no delete eu pego o id e vejo se o usuario pode acessar
         * caso não meto um not found, não precisa ser não autorizado, pois se for
         * a pessoa vai saber que o registro existe mas ela não pode acessar,
         * o not found ela nem sabe se existe.
         */
        if (in_array($this->request->action, ['edit', 'delete'])) {
            if (!$this->Usuarios->exists([
                'id' => (int)$this->request->usuarioId,
                'grupo_id' => (int)$this->Auth->user('grupo_id'),
                'deletado' => false
            ])) {
                throw new NotFoundException();
            }
        }

        $this->Auth->allow([
            'login',
            'logout',
            'esqueciSenha',
            'redefinirSenha'
        ]);

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
        $this->breadcrumbSet('Usuarios.index', ['controller' => 'Usuarios', 'action' => 'index']);

        $find = $this->Usuarios->todosVivosDoMeuGrupo('all', $this->Auth->user())
            ->contain([
                'QuemGravou',
                'Cargos',
                'GruposDeAcessos',
                'Lojas' => function($query) {
                    return $query
                        ->where([
                            'Lojas.deletado' => false
                        ])
                        ->order(['Lojas.nome']);
                }
            ])
            ->order([
                'Usuarios.nome'
            ]);

        if ($this->request->query('q')) {
            $q = '%' . str_replace(' ', '%', $this->request->query('q')) . '%';
            $find
                 ->where([
                     'OR' => [
                         'Usuarios.nome LIKE' => $q,
                         'Usuarios.email LIKE' => $q
                     ]
                 ]);
        }

        if ($this->request->query('cargos')) {
            $find->matching('Cargos', function($query) {
                return $query->where(['Cargos.id IN' => $this->request->query('cargos')]);
            });
        }

        if ((int)$this->request->query('loja')) {
            $find
                ->matching('Lojas', function($query) {
                    return $query
                        ->where([
                            'Lojas.id' => $this->request->query('loja'),
                        ]);
                })
                ->group('Usuarios.id');
        }

        $usuarios = $this->paginate($find);

        $cargos = $this->Usuarios->Cargos->todosAtivos('list')
            ->order(['Cargos.ordem']);

        $lojas = $this->Usuarios->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user());
    
        $this->set(compact('usuarios', 'cargos', 'lojas'));
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Usuarios.index', ['action' => 'index']);

        $usuario = $this->Usuarios->newEntity(null, ['associated' => ['Lojas']]);

        if ($this->request->is('post')) {
            // Para validar lojas
            $this->request->data['grupo_id'] = $this->Auth->user('grupo_id');

            // dd($this->request->getData());
            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData());

            $usuario->set('culpado_novo_id', $this->Auth->user('id'));

            if ($this->Usuarios->save($usuario)) {

                /**
                 * Devo salvar o log depois de salvar o usuario para eu pegar o id dele
                 */
                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $dataLog = [
                    'modulo_id' => 2,
                    'logs_tipo_id' => 3,
                    'table_name' => 'usuarios',
                    'ref' => $usuario->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $usuario);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);
                
                return $this->redirect($this->breadcrumbRedirect('Usuarios.index', ['action' => 'index']));
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $cargos = $this->Usuarios->Cargos->todosAtivos('all')
            ->order(['Cargos.ordem']);
        $lojas = $this->Usuarios->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())->order(['Lojas.nome']);

        $gruposDeAcessos = $this->Usuarios->GruposDeAcessos->todosDoMeuGrupo('list', $this->Auth->user())
            ->order(['GruposDeAcessos.nome']);

        $this->set(compact('usuario', 'cargos', 'lojas', 'gruposDeAcessos', 'breadcrumb'));

        $this->viewBuilder()->template('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit()
    {
        $breadcrumb['index'] = $this->breadcrumbRedirect('Usuarios.index', ['action' => 'index']);
        /**
         * Contain quem criou para montar o log
         * @var \Cake\ORM\Entity\Usuario
         */
        $usuario = $this->Usuarios->get($this->request->usuarioId, ['contain' => ['Lojas', 'QuemGravou', 'Cargos', 'GruposDeAcessos']]);
        // dd($usuario);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $this->request->data['grupo_id'] = $this->Auth->user('grupo_id');

            // debug($this->request->getData());
            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData());
            // dd($usuario);

            $usuarioAntesDeSalvar = clone $usuario;
            
            if ($this->Usuarios->save($usuario)) {

                //////////////////
                // SALVANDO LOG //
                //////////////////
                $this->loadModel('Logs');

                $dataLog = [
                    'modulo_id' => 2,
                    'logs_tipo_id' => 2,
                    'table_name' => 'usuarios',
                    'ref' => $usuario->id,
                    'autor_id' => $this->Auth->user('id'),
                    'grupo_id' => $this->Auth->user('grupo_id'),
                ];
                $dataLog = $this->Logs->patchData($dataLog, $usuarioAntesDeSalvar);
                $log = $this->Logs->newEntity($dataLog);
                $this->Logs->saveOrFail($log);            
                //////////////////
                // FIM SALVANDO LOG //
                //////////////////

                $this->Flash->set('O Usuário foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('Usuarios.index', ['action' => 'index']));
            }
            $this->Flash->set('O usuário não foi salvo.', ['element' => 'Painel/error']);
        }

        $cargosSelecionadosIds = (new Collection($usuario->cargos))->extract('id')->toArray();
        $cargos = $this->Usuarios->Cargos->todosAtivos('all')
            ->order(['Cargos.ordem']);

        $cargos->each(function($value) use ($cargosSelecionadosIds){
            $value->checked = (in_array($value->id, $cargosSelecionadosIds));
        });

        $lojas = $this->Usuarios->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user());

        $gruposDeAcessos = $this->Usuarios->GruposDeAcessos->todosDoMeuGrupo('list', $this->Auth->user())
            ->order(['GruposDeAcessos.nome']);

        $this->set(compact('usuario', 'cargos', 'lojas', 'gruposDeAcessos', 'breadcrumb'));

        $this->viewBuilder()->template('form');
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $this->request->allowMethod(['post', 'delete']);

        /**
         * Deve conter o QuemGravou para montar o log
         * @var Usuario
         */
        $usuario = $this->Usuarios->get($this->request->usuarioId, ['contain' => 'QuemGravou']);

        /**
         * SALVANDO LOG
         */
        $this->loadModel('Logs');

        $dataLog = [
            'modulo_id' => 2,
            'logs_tipo_id' => 1,
            'table_name' => 'usuarios',
            'ref' => $usuario->id,
            'autor_id' => $this->Auth->user('id'),
            'grupo_id' => $this->Auth->user('grupo_id'),
        ];

        $dataLog = $this->Logs->patchData($dataLog, $usuario);
        $log = $this->Logs->newEntity($dataLog);
        $this->Logs->saveOrFail($log);


        if ($this->Usuarios->customDelete($usuario)) {
            $this->Flash->set(__('O Usuário foi deletado.'), ['element' => 'Painel/success']);
        } else {
            $this->Flash->set(__('O Usuário não foi deletado.'), ['element' => 'Painel/error']);
        }

        return $this->redirect($this->breadcrumbRedirect('Usuarios.index', ['action' => 'index']));
    }

    public function alterarSenha()
    {
        $usuario = $this->Usuarios->get($this->Auth->user('id'), ['contain' => 'Grupos']);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $usuario = $this->Usuarios->patchEntity($usuario, [
                'senha_atual' => $this->request->getData('senha_atual'),
                'nova_senha' => $this->request->getData('nova_senha'),
                'confirmar_nova_senha' => $this->request->getData('confirmar_nova_senha'),
                'senha' => $this->request->getData('nova_senha')
            ]);

            // Para validar
            $usuario->exige_confirmar_senha_atual = true;

            if ($this->Usuarios->save($usuario)) {
                $this->Flash->set('A Senha foi alterada.', ['element' => 'Painel/success']);

                try {
                    $this->getMailer('Usuarios')
                        ->send('senhaAlterada', [$usuario]);
                } catch (\Exception $e) {
                    // Só joga o erro se for debug pra gente ver
                    // na produção segue o jogo
                    dd($e->getMessage());
                }

                return $this->Redirect(['action' => 'alterarSenha']);
            } else {
                $this->Flash->set('A Senha não foi alterada.', ['element' => 'Painel/error']);
            }
        }

        $this->set(compact('usuario'));
    }

    public function alterarEmail()
    {
        $usuario = $this->Usuarios->get($this->Auth->user('id'), ['contain' => ['Grupos']]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $usuario = $this->Usuarios->patchEntity($usuario, [
                'email' => $this->request->getData('email')
            ]);

            $emailAntigo = $usuario->getOriginal('email');

            if ($this->Usuarios->save($usuario)) {
                $this->Flash->set('O Email foi alterado.', ['element' => 'Painel/success']);

                $emailNovo = $usuario->email;

                if ($emailAntigo != $emailNovo) {
                    $this->getMailer('Usuarios')->send('emailAlterado', [$usuario, $emailAntigo, $emailAntigo, $emailNovo]);
                    $this->getMailer('Usuarios')->send('emailAlterado', [$usuario, $emailNovo, $emailAntigo, $emailNovo]);
                }

                return $this->Redirect(['action' => 'alterarEmail']);
            } else {
                $this->Flash->set('O Email não foi alterado.', ['element' => 'Painel/error']);
            }
        }

        $this->set(compact('usuario'));
    }

    public function esqueciSenha()
    {
        // Quando ele postar se não enviar o email passa direto tb e nada acontece
        if ($this->request->is(['patch', 'post', 'put']) && $this->request->getData('email')) {

            $usuario = $this->Usuarios->find('all')
                ->where([
                    'Usuarios.email' => $this->request->getData('email'),
                    'Usuarios.deletado' => false
                ])
                ->contain([
                    'Grupos' => function($query) {
                        return $query
                            ->where(['Grupos.slug' => $this->request->getParam('grupo_slug')]);
                    }
                ])
                ->first();

            if ($usuario) {

                /**
                 * Pego a entidade do usuario e coloca os dados para redefinir a senha e salvar
                 * @var [Object]
                 */
                $usuario = $this->Usuarios->patchEntityRedefinirSenha($usuario);
                try {
                    $this->getMailer('Usuarios')
                        ->send('recuperarSenha', [$usuario]);
                } catch (\Exception $e) {
                    dd($e->getMessage());
                    throw new BadRequestException("Ocorreu um erro ao enviar o Email, por favor tente novamente.");
                }

                if ($this->Usuarios->save($usuario)) {

                    $this->Flash->set(__('Você receberá uma mensagem em <strong>{0}</strong> com as instruções para redefinir a senha', h($usuario->email)), ['element' => 'Painel/inline/success', 'key' => 'auth', 'escape' => false]);

                    return $this->Redirect(['action' => 'esqueciSenha']);
                } else {
                    $this->Flash->set('Ocorreu um erro, por favor tente novamente', ['element' => 'Painel/error']);
                }

            } else {
                $this->Flash->set('O Email informado não está cadastrado no sistema', ['element' => 'Painel/inline/error', 'key' => 'auth']);
            }
        }
    }

    public function redefinirSenha()
    {
        $usuario = $this->Usuarios->find()
            ->where([
                'grupo_id' => $this->grupo->id,
                'redefinir_senha_email_hash' => $this->request->emailHash
            ])
            ->contain(['Grupos'])
            ->first();

        if (!$usuario || $usuario->redefinir_senha_token != $this->request->token) {
            throw new ForbiddenException('Token inválido');
        }

        if ($usuario->redefinirSenhaIsExpirado) {
            throw new ForbiddenException('Solicitação expirada');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {

            $usuario = $this->Usuarios->patchEntity($usuario, [
                'nova_senha' => $this->request->getData('nova_senha'),
                'confirmar_nova_senha' => $this->request->getData('confirmar_nova_senha'),
                'redefinir_senha_token' => null,
                'redefinir_senha_timestamp' => null,
                'redefinir_senha_email_hash' => null,
                'senha' => $this->request->getData('nova_senha')
            ]);

            if ($this->Usuarios->save($usuario)) {

                try {
                    $this->getMailer('Usuarios')
                        ->send('senhaAlterada', [$usuario]);
                } catch (\Exception $e) {
                    // Só joga o erro se for debug pra gente ver
                    // na produção segue o jogo
                    dd($e->getMessage());
                }

                $this->Flash->set('A senha foi redefinida com sucesso, você já pode entrar no sistema com a nova senha.', ['element' => 'Painel/inline/default', 'key' => 'auth']);
                return $this->redirect(['controller' => 'Usuarios', 'action' => 'login']);
            }
        }

        $this->set(compact('usuario'));
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->set('Combinação email/senha incorreta.', ['element' => 'Painel/inline/error', 'key' => 'auth']);
            }
        }
    }
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
}
