<?php
    $title = ($usuario->isNew()) ? 'Adicionar Usuário' : __('Editar Usuário "{0}"', $usuario->nome, $grupo->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Redes' => ['controller' => 'Grupos', 'action' => 'index'],
        'Usuários do Grupo "'.$grupo->nome.'"' => ['controller' => 'Usuarios', 'action' => 'index', 'grupoId' => $this->request->grupoId],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($usuario, ['novalidate' => true]) ?>

            <?= $this->Form->input('grupo_id', ['type' => 'hidden', 'value' => $this->request->grupoId]) ?>

            <div class="panel panel-default">
                <div class="panel-title">
                    Dados do usuário
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('nome') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('email', ['label' => 'Email']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= ($usuario->isNew()) ? $this->Form->input('senha', ['type' => 'password', 'label' => 'Senha']) : '' ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('cargos._ids', ['empty' => 'Selecione o cargo:', 'multiple' => 'multiple', 'class' => 'select2']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                        $options = [
                            'type' => 'checkbox',
                            'hiddenField' => true,
                            'templates' => [
                                'checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>',
                            ],
                        ];
                        if ($usuario->isNew()) {
                            $options['checked'] = true;
                        }
                        echo $this->Form->input('ativo', $options);
                    ?>
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
