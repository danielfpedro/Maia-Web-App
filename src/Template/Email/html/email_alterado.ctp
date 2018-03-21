<?php
    $this->assign('logo', $usuario->grupo->logo_email_path);
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <strong><?= $usuario->short_name ?></    strong>,</p>

    <p>O email de acesso da sua conta acaba de ser alterado de <strong><?= $emailAntigo?></strong> para <strong><?= $emailNovo ?></strong>.</p>

<?= $this->end() ?>
