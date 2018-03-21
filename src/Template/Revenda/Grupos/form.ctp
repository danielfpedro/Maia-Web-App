<?php
    $title = ($grupo->isNew()) ? 'Adicionar Grupo' : __('Editar Grupo "{0}"', $grupo->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('../lib/preenche_cidades/dist/plugin', ['block' => true]);
    $this->Html->script('Painel/endereco', ['block' => true]);
    $this->Html->script('Controle/Grupos/form', ['block' => true]);

    $this->Html->scriptStart(['block' => true]);
        echo "$('#cnpj').mask('99.999.999/9999-99');";
    $this->Html->scriptEnd();
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
                    Dados do Grupo
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('nome') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('slug') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('segmento_id', ['empty' => 'Selecione:', 'value' => null]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-title">
                    Dados de Cobrança
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('cnpj', ['label' => 'CNPJ']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('inscricao_estadual', ['label' => 'Inscrição Estadual']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('nome_fantasia') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('razao_social', ['label' => 'Razão Social']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('email_financeiro') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('cep', [
                                'label' => 'CEP',
                                'append' => '<button type="button" data-url="' . $this->Url->build(['controller' => 'Enderecos', 'action' => 'todosPeloCep', 'prefix' => 'webservice_helpers', '_ext' => 'json']) . '" class="btn btn-default btn-preenche-endereco"><span class="fa fa-map-marker"></span></button>',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('estados', [
                                'empty' => 'Seleciona o Estado:',
                                'label' => 'Estado',
                                'data-url' => $this->Url->build(['controller' => 'Cidades', 'action' => 'todasDoEstado', 'prefix' => 'webservice_helpers', '_ext' => 'json']),
                                'value' => (!$grupo->isNew() && $grupo->cidade) ? (int)$grupo->cidade->estado_id : null, 'data-preselect-city' => (!$grupo->isNew() && $grupo->cidade) ? (int)$grupo->cidade->id : '']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('cidade_id', ['empty' => 'Selecione a cidade:']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('bairro') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('endereco') ?>
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
                        if ($grupo->isNew()) {
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