<?php
    $this->assign('logo', $usuario->grupo->logo_email_path);
    $this->assign('temBotao', false);
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <strong><?= $usuario->short_name ?></strong>,</p>

    <p>A sua senha de acesso acabou de ser alterada.</p>
<?= $this->end() ?>
