<?php
namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Routing\Router;

use Cake\Mailer\Email;

/**
 * Notificacoes mailer.
 */
class VisitasMailer extends Mailer
{

    /**
     * Mailer's name.
     *
     * @var string
     */
    static public $name = 'Notificacoes';

    public function encerramentoComRespostaCritica($to, $visita, $perguntasComRespostaCritica)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('default')
            ->setLayout('relatorios_print_friendly')
            ->setTemplate('Visitas/email_critico')
            ->setSubject('AVISO ITEM CRÍTICO - AUDITORIA')
            ->setFrom([$config['from'] => $visita->quem_gravou->grupo->nome])
            ->setTo($to, $visita->loja->nome)
            ->setViewVars([
                'visita' => $visita,
                'perguntasComRespostasCritica' => $perguntasComRespostaCritica,
                'urlSistema' => [
                    'controller' => 'Usuarios',
                    'action' => 'login',
                    'prefix' => 'painel',
                    'grupo_slug' => $visita->quem_gravou->grupo->slug
                ]
            ]);
    }

    public function encerramento($to, $visita)
    {
        $config = Email::config('default');

        $buttonUrl = $visita->getUrlPublicaDoResultado();
        $buttonUrl['grupo_slug'] = $visita->quem_gravou->grupo->slug;

        $subject = 'Resultado da visita';
        return  $this
            ->setProfile('default')
            ->setTemplate('Visitas/resultado')
            ->setSubject($subject)
            ->setFrom([$config['from'] => $visita->quem_gravou->grupo->nome])
            ->setTo($to, $visita->loja->nome)
            ->setViewVars([
                'visita' => $visita,
                'title' => $subject,
                'buttonUrl' => Router::url($buttonUrl, ['fullbase' => true]),
                'urlSistema' => [
                    'controller' => 'Usuarios',
                    'action' => 'login',
                    'prefix' => 'painel',
                    'grupo_slug' => $visita->quem_gravou->grupo->slug
                ]
            ], ['fullbase' => true]);
    }

    public function prazoAlterado($visita, $prazoAntigo, $quemAlterou)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('default')
            ->setTemplate('visitaPrazoAlterado')
            ->setSubject('Alteração do prazo da visita')
            ->setTo($visita->usuario->email, $visita->usuario->short_name)
            ->setFrom([$config['from'] => $quemAlterou->grupo->nome])
            ->set([
                'visita' => $visita,
                'prazoAntigo' => $prazoAntigo,
                'quemAlterou' => $quemAlterou
            ]);
    }

    public function nova($visita, $quemCriou)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('default')
            ->setTemplate('Visitas/nova')
            ->setSubject('Você tem uma nova visita')
            ->setTo($visita->usuario->email, $visita->usuario->short_name)
            ->setFrom([$config['from'] => $quemCriou->grupo->nome])
            ->set([
                'visita' => $visita,
                'quemCriou' => $quemCriou
            ]);
    }
    public function cancelada($visita, $quemCancelou)
    {
        $config = Email::config('default');

        return  $this
            ->setProfile('default')
            ->setTemplate('Visitas/cancelada')
            ->setSubject('Visita cancelada')
            ->setTo($visita->usuario->email, $visita->usuario->short_name)
            ->setFrom([$config['from'] => $quemCancelou->grupo->nome])
            ->set([
                'visita' => $visita,
                'quemCancelou' => $quemCancelou
            ]);
    }

}
