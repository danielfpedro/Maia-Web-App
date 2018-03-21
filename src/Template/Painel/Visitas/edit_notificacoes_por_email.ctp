<?php

    $this->Html->css('../js/aehlke-tag-it/css/jquery.tagit', ['block' => true]);
    $this->Html->script('aehlke-tag-it/js/tag-it.min', ['block' => true]);

    $title = __('Editar Notificações por email "{0} / {1}"', $visita->loja->nome, $visita->usuario->short_name);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->scriptStart(['block' => true]);
        echo "$('.has-tagit').tagit();";
    $this->Html->scriptEnd();
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Visitas' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($visita, ['novalidate' => true]);?>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('grupos_de_emails._ids', ['class' => 'select2', 'width' => '100%', 'multiple' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('emails_resultados_extras', ['type' => 'text', 'class' => 'has-tagit']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('emails_criticos_extras', ['type' => 'text', 'class' => 'has-tagit']) ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body text-right">
                    <?= $this->Koletor->btnSalvar() ?>
                </div>
            </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
