<?php
    $title = __('Customização da Tela de Login de "{0}"', $grupo->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

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
                    Tela de Login
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('login_logo_file_placeholder', ['label' => 'Logo', 'type' => 'file', 'style' => 'width: 100%']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('login_logo_width', ['label' => 'Logo Largura', 'class' => '']) ?>
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