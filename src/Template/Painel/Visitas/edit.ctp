<?php
    $title = __('Editar Visita "{0} / {1}"', $visita->loja->nome, $visita->usuario->short_name);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Visitas' => $breadcrumb['index'],
    ]
]) ?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($visita, ['novalidate' => true]);?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $this->Form->input('requerimento_localizacao', [
                                'type' => 'select',
                                'label' => '*Requerimento de localização',
                                'options' => $visita->getRequerimentoLocalizacaoOptions(),
                                'templates' => [
                                    'label' => '<label{{attrs}}><span class="fa fa-map-marker"></span> {{text}}</label>'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <label for="prazo-placeholder">*Prazo</label>
                            <?= $this->Form->input('prazo_placeholder', ['type' => 'text', 'class' => 'date', 'label' => false, 'placeholder' => '__/__/__']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                        $options = ['type' => 'checkbox', 'hiddenField' => true, 'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']];
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
