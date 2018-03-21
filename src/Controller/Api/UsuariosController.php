<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;

use Cake\Event\Event;
// Exceptions
use Cake\Network\Exception\UnauthorizedException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Routing\Router;

// Email
use Cake\Mailer\MailerAwareTrait;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsuariosController extends AppController
{

    // Traits
    use MailerAwareTrait;

    public function initialize()
    {
        parent::initialize();
        // Actions que não precisar estar logado para acessar
        $this->Auth->allow([
            'getToken',
            'recuperarSenha',
            'getGruposByEmail'
        ]);

        $this->Security->config('unlockedActions', ['alterarSenha', 'getGruposByEmail', 'getToken']);
    }

    public function getGruposByEmail() {
        // $subquery = $this->Usuarios->find()
        //     ->select(['Usuarios.grupo_id'])
        //     ->where([
        //         'Usuarios.email' => $this->request->data('email'),
        //         'Usuarios.deletado' => false,
        //         'Usuarios.ativo' => true,
        //     ])
        //     ->matching('Cargos', function($query) {
        //         return $query->where(['Cargos.id IN' => [1, 2]]);
        //     })
        //     ->group('Usuarios.id');

        $grupos = $this->Usuarios->Grupos->find('all')
            ->select([
                'Grupos.id',
                'Grupos.nome'
            ])
            ->where([
                'Grupos.ativo' => true,
                // 'Grupos.id IN' => $subquery
            ])
            ->matching('Usuarios', function($query) {
                return $query
                    ->where([
                        'Usuarios.email' => $this->request->data('email'),
                        'Usuarios.deletado' => false,
                        'Usuarios.ativo' => true
                    ])
                    ->matching('Cargos', function($query) {
                        return $query->where(['Cargos.id IN' => [1, 2]]);
                    })
                    ->group(['Usuarios.id']);
            })
            ->group(['Grupos.id'])
            ->order(['Grupos.nome' => 'ASC']);

        $this->set(compact('grupos'));
        $this->set('_serialize', 'grupos');
    }

    /**
     * Recebe email e senha e retorna os dados do usuario e o JWT. É usado para 'logar' o usuario no app
     */
    public function getToken()
    {
        // Pego o usuário baseado no finder do nosso Auth
        $usuario = $this->Auth->identify();

        // Se não identificou a gente joga o erro
        if (!$usuario) {
            throw new UnauthorizedException('A combinação Email/Senha é inválida.');
        }

        $grupoCompleto = $this->Usuarios->Grupos->get($usuario['grupo_id']);
        $grupo = [];
        $grupo['id'] = $grupoCompleto->id;
        $grupo['app_bgcolor'] = $grupoCompleto->app_bgcolor;
        $grupo['app_statusbar_color'] = $grupoCompleto->app_statusbar_color;
        $grupo['app_font_color'] = $grupoCompleto->app_font_color;
        $grupo['logo_full_path'] = Router::url('/', ['fullbase' => true]) . ltrim($grupoCompleto->logo_navbar_path, '/');

        // Caso identificou o usuário retornamos a mensagem de sucesso com os dados do usuário
        $token = $this->Usuarios->generateJwt($usuario);
        $this->set(compact('usuario', 'grupo', 'token'));
        $this->set('_serialize', ['usuario', 'grupo', 'token']);
    }

    /**
     * Pegamos o usuario do resquest, salvamos algumas informações e mandamos o email
     * com as informações para ele recuperar a senha.
     */
    public function recuperarSenha()
    {
        // Pego o usuário
        $usuario = $this->Usuarios->find()
            ->where([
                'Usuarios.email' => $this->request->getData('email'),
                'Usuarios.grupo_id' => (int)$this->request->getData('grupo_id'),
                'Usuarios.ativo' => true,
                'Usuarios.deletado' => false,
            ])
            ->contain(['Grupos'])
            ->first();

        // Se não existir eu jogo um erro
        if (!$usuario) {
            throw new RecordNotFoundException('O Email informado não pertence a nenhum usuário cadastrado no sistema.');
        }

        // Aqui eu pego os dados do usuário e adiciono `email_hash`, `token` e `recuperar_senha_timestamp`
        // que são gerados baseado na lógica do sistema
        $usuario = $this->Usuarios->patchEntityRedefinirSenha($usuario);

        // Salvo os dados do usuário ja que a $usuario já tem os valores para recuperar a senha
        $this->Usuarios->saveOrFail($usuario);

        // Uso try pois o usuário não precisa saber o erro de fato "smtp falhou, senha errado etc..." então uso
        // o try e jogo uma mensagem de erro generica para ele
        try {
            $this->getMailer('Usuarios')
                ->send('recuperarSenha', [$usuario]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            throw new BadRequestException("Ocorreu um erro ao enviar o Email, por favor tente novamente.");
        }

        // Se nenhum erro foi jogado chegamos aqui, e dados o response de sucesso
        $response = $this->responseSuccess(__('As instruções para recuperar a sua senha foram enviadas para o email "{0}".', $usuario->email));
        $this->set(compact('response'));
        $this->set('_serialize', 'response');

    }

    // O Auth no app guarda dados simples como nome para mostrar em alguma tela
    // aqui a gente atualiza os dados
    public function atualizaDadosAuth()
    {
        $usuario = $this->Usuarios->getValidoById($this->Auth->user('id'))
            ->select(['nome'])
            ->first();

        if (!$usuario) {
            throw new BadRequestException("Usuário não encontrado.");
        }

        $this->set(compact('usuario'));
        $this->set('_serialize', ['usuario']);
    }

    public function alterarSenha()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {

            $usuarioId = $this->Auth->user('id');
            $grupoId = $this->Auth->user('grupo_id');

            // Busco os dados do usuário e
            $usuario = $this->Usuarios->find()
                ->select([
                    'Usuarios.id',
                    'Usuarios.senha',
                ])
                ->where([
                    'Usuarios.id' => $usuarioId,
                    'Usuarios.grupo_id' => $grupoId,
                    'Usuarios.deletado' => false,
                    'Usuarios.ativo' => true
                ])
                ->first();

            // Isso nunca vai acontecer em condições normais então a resposta de erro pode ser bem simples
            if (!$usuario) {
                throw new BadRequestException("Usuário não encontrado.");
            }

            // Faço o patch com os dados, serve para cair na validação de dados (se senha atual está correta, foi repetido corretamente etc)
            $usuario = $this->Usuarios->patchEntity($usuario, [
                'senha_atual' => $this->request->getData('senha_atual'),
                'nova_senha' => $this->request->getData('nova_senha'),
                'confirmar_nova_senha' => $this->request->getData('confirmar_nova_senha'),
                'senha' => $this->request->getData('nova_senha')
            ]);
            // Para validar
            $usuario->exige_confirmar_senha_atual = true;

            // Tenta salvar
            if (!$this->Usuarios->save($usuario)) {
              foreach ($usuario->errors() as $key => $erro) {
                // Pego o primeiro erro e ta lindo
                throw new BadRequestException($erro[0]);
              }
            }
        } else {
            throw new MethodNotAllowedException();
        }

        // Se nenhum erro foi jogado chegamos aqui, e dados o response de sucesso
        $response = $this->responseSuccess('A sua senha foi alterada com sucesso!');
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

    // Usuario bate aqui só pra garantirmos que ele está online
    public function checkConnection()
    {
        $response = $this->responseSuccess('connected');
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }

}
