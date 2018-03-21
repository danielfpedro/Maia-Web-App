<?php
namespace App\Mailer;

use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;

use Cake\Core\Configure;

/**
 * Usuarios mailer.
 */
class UsuariosMailer extends Mailer
{

    /**
     * Mailer's name.
     *
     * @var string
     */
    static public $name = 'Usuarios';

    public function recuperarSenha($usuario)
    {
        $config = Email::config('default');

        $buttonUrl = [
            'controller' => 'Usuarios',
            'action' => 'redefinirSenha',
            'prefix' => 'painel',
            'emailHash' => $usuario->redefinir_senha_email_hash,
            'token' => $usuario->redefinir_senha_token,
            // quando chama da api não tem o grupo_slug(deveria ter implementar) entao tenho que injetar pq
            // esse mailer é usado api e painel
            'grupo_slug' => $usuario->grupo->slug
        ];

        return  $this
            ->setProfile('default')
            ->setTo($usuario->email, $usuario->short_name)
            ->setFrom([$config['from'] => $usuario->grupo->nome])
            ->setTemplate('Usuarios/recuperar_senha')
            ->setSubject('Solicitação para redefinir senha')
            ->setViewVars([
                'usuario' => $usuario,
                'buttonUrl' => Router::url($buttonUrl, ['fullbase' => true]),
                'urlSistema' => [
                    'controller' => 'Usuarios',
                    'action' => 'login',
                    'prefix' => 'painel',
                    // quando chama da api não tem o grupo_slug(deveria ter implementar) entao tenho que injetar pq
                    // esse mailer é usado api e painel
                    'grupo_slug' => $usuario->grupo->slug
                ]
            ]);
    }

    public function senhaAlterada($usuario)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('default')
            ->setTo($usuario->email, $usuario->short_name)
            ->setFrom([$config['from'] => $usuario->grupo->nome])
            ->setTemplate('Usuarios/senha_alterada')
            ->setSubject('Senha de acesso alterada')
            ->setViewVars([
                'usuario' => $usuario
            ]);
    }

    public function emailAlterado($usuario, $to, $emailAntigo, $emailNovo)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('emailAlterado')
            ->setTo($to, $usuario->short_name)
            ->setFrom([$config['from'] => $usuario->grupo->nome])
            ->set(compact('usuario', 'emailAntigo', 'emailNovo'));
    }

    public function criado($usuario)
    {
        $config = Email::config('default');

        $buttonUrl = [
            'controller' => 'Usuarios',
            'action' => 'login',
            'prefix' => 'painel',
            // quando chama da api não tem o grupo_slug(deveria ter implementar) entao tenho que injetar pq
            // esse mailer é usado api e painel
            'grupo_slug' => $usuario->grupo->slug
        ];

        return  $this
            ->setProfile('default')
            ->setTo($usuario->email, $usuario->short_name)
            ->setFrom([$config['from'] => $usuario->grupo->nome])
            ->setTemplate('Usuarios/criado')
            ->setSubject($usuario->grupo->nome . ' Auditoria acaba de criar um usuário para você!')
            ->setViewVars([
                'usuario' => $usuario,
                'buttonUrl' => Router::url($buttonUrl, ['fullbase' => true]),
            ]);
    }

}
