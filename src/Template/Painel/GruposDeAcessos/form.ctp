<?php
    $title = ($grupoDeAcesso->isNew()) ? 'Adicionar Grupo de Acesso' : __('Editar Grupo de Acesso "{0}"', $grupoDeAcesso->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Grupos de Acessos' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($grupoDeAcesso, ['novalidate' => true]) ?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados do Grupo de Acesso
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                                echo $this->Form->input('nome');
                            ?>                            
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
