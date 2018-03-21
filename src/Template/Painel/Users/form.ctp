<?php
    $title = ($usuario->isNew()) ? 'Adicionar Usuário' : __('Editar Usuário "{0}"', $usuario->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Usuários' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($usuario, ['novalidate' => true]) ?>

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
                <div class="panel-title">
                    Cargos
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php foreach ($cargos as $key => $value): ?>
                                <?= $this->Form->input('cargos._ids.' . (int)$value->id, ['label' => $value->nome, 'value' => (int)$value->id, 'checked' => ($value->checked), 'class' => '', 'type' => 'checkbox', 'templates' => [
                                        'checkboxContainer' => '<div class="checkbox checkbox-success {{required}}">{{content}}</div>'
                                    ]]) ?>    
                                <div style="margin-bottom: 35px;" class="kode-alert alert-default-light"><?= $value->descricao ?></div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default painel-lojas">
                <div class="panel-body">
                    <?= $this->Form->input('grupos_de_acessos._ids', [
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'data-placeholder' => 'Nenhum'
                        ])
                    ?>
                </div>
            </div>

            <div class="panel panel-default painel-lojas">
                <div class="panel-title">
                    Lojas
                </div>
                <div class="panel-body">
                    <div class="container-lojas">
                        <?= $this->Form->input('lojas._ids', [
                            'multiple' => 'checkbox',
                            'label' => false,
                            'templates' => [
                                'checkboxContainer' => '<div class="checkbox checkbox-success {{required}}">{{content}}</div>',
                            ]
                        ]) ?>
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
