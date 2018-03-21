<?php
    $title = ($checklist->isNew()) ? 'Adicionar Checklist' : __('Editar Checklist "{0}"', $checklist->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Questionários' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($checklist, [
            'novalidate' => true
        ]);
        ?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados da Checklist
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('nome') ?>
                            <?php if ($grupoCustomizationData->id == 1): ?>
                                <?= $this->Form->input('segmento_id', ['empty' => 'Selecione o segmento:']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('observacao', ['label' => 'Observação']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $this->Form->input('minimo_esperado', ['label' => 'Mínimo Esperado', 'help' => 'Valor em porcentagem']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->input('grupos_de_acessos._ids', [
                                'multiple' => true,
                                'class' => 'select2',
                                'data-placeholder' => 'Nenhum',
                                'help' => 'Se você não selecionar nenhum grupo de acesso o questionário ficará acessível para todos os usuários.'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                        $options = [
                            'label' => 'Permitir visita sem agendamento',
                            'type' => 'checkbox',
                            'hiddenField' => true,
                            'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>'
                            ]
                        ];

                        echo $this->Form->input('sem_agendamento_flag', $options);
                    ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                        $options = ['type' => 'checkbox', 'hiddenField' => true, 'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']];
                        if ($checklist->isNew()) {
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
