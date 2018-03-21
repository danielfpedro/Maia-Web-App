<?php
    $title = 'Alterar Email';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
    ]
]) ?>

<div class="row">
    <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked menu-configuracoes-de-senha">
            <li class="active">
                <?= $this->Html->link('Alterar Email', ['controller' => 'Usuarios', 'action' => 'alterarEmail']) ?>
            </li>
            <li class="">
                <?= $this->Html->link('Alterar Senha', ['controller' => 'Usuarios', 'action' => 'alterarSenha']) ?>
            </li>
        </ul>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <?= $this->Form->create($usuario, ['novalidate' => true]) ?>
                    <div class="panel panel-default">
                        <div class="panel-title">

                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->input('email') ?>
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
    </div>
</div>
