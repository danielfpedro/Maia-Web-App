<?php
    $this->assign('logo', $visita->quem_gravou->grupo->logo_email_path);
    $this->assign('temBotao', true);
    $this->assign('buttonText', 'Visualizar Visita');
?>

<?= $this->start('textoPrincipal') ?>
<p>O auditor <strong><em><?= $visita->usuario->nome ?></em></strong> finalizou agora (<em><strong><?= ($visita->dt_encerramento) ? $visita->dt_encerramento->format('d/m/y \à\s H:i') : '-' ?></strong></em>) uma visita na loja <em><strong><?= $visita->loja->nome ?></strong></em>, clique no botão abaixo para visualizar o resultado:</p>

<?= $this->end() ?>
