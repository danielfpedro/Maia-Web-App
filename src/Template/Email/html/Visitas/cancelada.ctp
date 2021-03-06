<?php
    $this->assign('logo', $quemCancelou->grupo->logo_email_path);
    $this->assign('temBotao', false);
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <?= $visita->usuario->short_name ?>,</p>

    <p>
        A sua visita para a loja <strong><?= $visita->loja->nome ?></strong> com prazo para <strong><?= $visita->prazo->format('d/m/y') ?></strong>
        acabou de ser <strong>CANCELADA</strong> por <strong><?= $quemCancelou->short_name ?></strong>.
     </p>
     <p>
        <address>
        <strong><?= $visita->loja->nome ?></strong><br>
        <?= $visita->loja->endereco ?>, <?= $visita->loja->bairro ?><br>
        <?= $visita->loja->cidade->nome ?> / <?= $visita->loja->cidade->uf ?><br>
        </address>
     </p>
<?= $this->end() ?>

<?= $this->start('textoExtra') ?>
<?= $this->end() ?>
