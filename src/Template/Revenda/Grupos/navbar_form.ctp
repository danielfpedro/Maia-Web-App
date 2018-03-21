<?php
    $title = __('Customização da Navbar para "{0}"', $grupo->nome);

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
                    Customização do Navbar do Painel
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('navbar_color', ['label' => 'Cor', 'class' => 'jscolor {hash: true, required: false, onFineChange:\'atualizaNavbarColor(this)\'}']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('navbar_font_color', ['label' => 'Cor da Fonte', 'class' => 'jscolor {hash: true, required: false, onFineChange:\'atualizaNavbarFontColor(this)\'}']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('navbar_logo_file_placeholder', ['label' => 'Logo', 'type' => 'file', 'style' => 'width: 100%']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('navbar_logo_width', ['label' => 'Logo Largura']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('navbar_logo_margin_top', ['label' => 'Logo Margem para o Topo']) ?>
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