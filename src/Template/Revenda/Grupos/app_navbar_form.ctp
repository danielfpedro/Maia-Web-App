<?php
    $title = __('Customização da Navbar do App para "{0}"', $grupo->nome);
    
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('jscolor/jscolor.min', ['block' => true]);
    $this->Html->script('Controle/Grupos/form', ['block' => true]);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Grupos' => ['action' => 'index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($grupo, ['novalidate' => true, 'type' => 'file']) ?>

            <div class="panel panel-default">
                <div class="panel-title">
                    Customização da Navbar do App
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('app_navbar_color', ['label' => 'Cor', 'class' => 'jscolor {hash: true, required: false}']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('app_navbar_font_color', ['label' => 'Cor da Fonte', 'class' => 'jscolor {hash: true, required: false}']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('app_navbar_logo_file_placeholder', ['label' => 'Logo', 'type' => 'file', 'style' => 'width: 100%']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('app_statusbar_color', ['label' => 'Cor da Statusbar', 'class' => 'jscolor {hash: true, required: false}']) ?>
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