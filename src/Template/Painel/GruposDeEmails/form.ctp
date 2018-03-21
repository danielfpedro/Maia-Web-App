<?php
    $title = ($gruposDeEmail->isNew()) ? 'Adicionar Grupo de Emails' : __('Editar Grupo de Emails "{0}"', $gruposDeEmail->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->css('../js/aehlke-tag-it/css/jquery.tagit', ['block' => true]);
    $this->Html->script('aehlke-tag-it/js/tag-it.min', ['block' => true]);

    $this->Html->script('Painel/GruposDeEmails/form', ['block' => true]);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
    'Grupos de Emails' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($gruposDeEmail, ['novalidate' => true]) ?>

            <div class="panel panel-default">
                <div class="panel-title">
                    Dados do Grupo
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('nome') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-title">
                    Vínculos
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('lojas._ids', ['label' => 'Lojas', 'data-placeholder' => 'Todas:', 'class' => 'select2']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('checklists._ids', ['label' => 'Questionários', 'data-placeholder' => 'Todos:', 'class' => 'select2']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-title">
                    Endereços de Emails
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('emails_resultados', ['label' => 'Resultados', 'type' => 'text', 'help' => 'Os endereços aqui cadastrados receberão um email com um link para o resultado completo da visita assim que ela for finalizada pelo Auditor.']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('emails_criticos', ['label' => 'Itens Críticos', 'type' => 'text', 'help' => 'Os endereços aqui cadastrados receberão um email com um resumo dos itens críticos da visita no próprio corpo do email assim que ela for finalizada pelo Auditor.']) ?>
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
