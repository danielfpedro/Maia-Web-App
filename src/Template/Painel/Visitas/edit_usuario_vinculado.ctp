<?php
    $title = __('Editar Usuário Vinculado da Visita "{0} / {1}"', $visita->loja->nome, $visita->usuario->short_name);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Visitas' => ['action' => 'index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($visita, ['novalidate' => true]);?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('usuario_vinculado_id', [
                                'type' => 'select',
                                'label' => 'Usuário vinculado',
                                'options' => $usuarios,
                                'empty' => 'Selecione o Usuário vinculado:',
                                'templates' => [
                                    'label' => '<label{{attrs}}><span class="fa fa-user"></span> {{text}}</label>'
                                ]
                            ]) ?>
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
