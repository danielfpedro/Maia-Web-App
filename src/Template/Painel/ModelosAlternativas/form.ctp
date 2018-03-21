<?php
    $title = ($modelosAlternativa->isNew()) ? 'Adicionar Modelo' : __('Editar Modelo "{0}"', $modelosAlternativa->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('Painel/setores', ['block' => true]);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Modelos de Alternativas' => $breadcrumb['index'],
    ]
]) ?>

<textarea style="display: none;" id="alternativas"><?= (isset($modelosAlternativa->alternativas_dos_modelos)) ? json_encode($modelosAlternativa->alternativas_dos_modelos) : '[]' ?></textarea>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-6">
        <?= $this->Form->create($modelosAlternativa, [
            'novalidate' => true
        ]); ?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados do modelo
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
                    Alternativas
                </div>
                <div class="panel-body">

                    <div id="carrega-alternativas">
                        <div></div>
                    </div>

                    <!-- Protótipo, serve para ser clona e trabalhado no jquery -->
                    <div class="row" id="container-alternativas" style="display: none;">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom: 15px; font-weight: bold; border-bottom: #eee 1px solid; padding-bottom: 10px;">
                                <div class="col-md-5">
                                    Alternativa
                                </div>
                                <div class="col-md-3">
                                    Valor
                                </div>
                                <div class="col-md-1 text-center">
                                    <span class="fa fa-camera"></span>
                                </div>
                                <div class="col-md-1 text-center">
                                    <span class="fa fa-exclamation-triangle"></span>
                                </div>
                                <div class="col-md-2"></div>
                            </div>
                            <div id="container-campos">
                                <div class="row alternativa-linha" style="margin-bottom: 18px; cursor: s-resize;">
                                    <div class="col-md-5">
                                        <input type="hidden" class="form-control pergunta-id">
                                        <div class="">
                                            <input type="text" placeholder="" class="form-control pergunta">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="">
                                            <input type="text" placeholder="" class="form-control valor">
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <div class="checkbox checkbox-primary" style="margin-top: 2px;">
                                            <input id="checkbox101" type="checkbox" class="tem-foto">
                                            <label for="checkbox101" class="label-tem-foto"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <div class="checkbox checkbox-primary" style="margin-top: 2px;">
                                            <input id="checkbox101" type="checkbox" class="item-critico">
                                            <label for="checkbox101" Class="label-item-critico"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <button type="button" class="btn btn-light btn-xs btn-icon btn-remove-questao" style="margin-top: 5px;" disabled>
                                            <span class="fa fa-times"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 35px;">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-add-questao"><span class="fa fa-plus"></span> Adicionar Alternativa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                        echo $this->Form->input('ativo', ['type' => 'checkbox', 'hiddenField' => true, 'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']]);
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
