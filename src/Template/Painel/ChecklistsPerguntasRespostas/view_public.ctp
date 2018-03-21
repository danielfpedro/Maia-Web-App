<?php
    $title = __('Visita "{0} | {1}"', h($visita->cod), h($visita->loja->nome));
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    echo $this->Html->css('../lib/lightbox2/dist/css/lightbox.min', ['block' => true]);
    echo $this->Html->script('../lib/lightbox2/dist/js/lightbox.min', ['block' => true]);
    echo $this->Html->script('Painel/lightbox_pt-br', ['block' => true]);

    echo $this->Html->script('Painel/respostas_view', ['block' => true]);

?>
<?= $this->element('Painel/ChecklistsPerguntasRespostas/respostas', ['isPublic' => true]) ?>
