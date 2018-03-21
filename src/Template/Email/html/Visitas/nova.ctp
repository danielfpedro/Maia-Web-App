<?php
    $this->assign('logo', $quemCriou->grupo->logo_email_path);
    $this->assign('temBotao', false);
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <?= $visita->usuario->short_name ?>,</p>

    <p>
        Você tem uma <strong>NOVA VISITA</strong> para fazer em <strong><?= $visita->loja->nome ?></strong> com prazo para <strong><?= $visita->prazo->format('d/m/y') ?></strong>.
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
    Acesse o aplicativo para ver todos os detalhes.
<?= $this->end() ?>
