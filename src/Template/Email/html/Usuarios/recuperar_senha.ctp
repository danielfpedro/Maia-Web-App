<?php
    $this->assign('logo', $usuario->grupo->logo_email_path);
    $this->assign('temBotao', true);
    $this->assign('buttonText', 'Redefinir Senha');
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <strong><?= $usuario->short_name ?></strong>, você esqueceu a sua senha?</p>

    <p>Você pode redefinir a sua senha clicando no botão abaixo.</p>
<?= $this->end() ?>

<?= $this->start('textoExtra') ?>
    <p>Caso não feito a solicitação desconsiderar este email.</p>
<?= $this->end() ?>
