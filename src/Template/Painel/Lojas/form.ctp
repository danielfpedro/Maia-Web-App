<?php
    $title = ($loja->isNew()) ? 'Adicionar Loja' : __('Editar Loja "{0}"', $loja->nome) ;
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('../lib/preenche_cidades/dist/plugin', ['block' => true]);
    $this->Html->script('Painel/endereco', ['block' => true]);

    $this->Html->script('https://maps.googleapis.com/maps/api/js?key=AIzaSyCjhVwkKaBB5fcGVaM6yrsGBbytEu2PP7s&libraries=places&callback=initMap', ['block' => true]);
    $this->Html->script('../lib/gmap3/dist/gmap3.min', ['block' => true, 'async', 'defer']);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Lojas' => $breadcrumb['index'],
    ]
]) ?>

<div class="main-panels-container">
    <?php
        echo $this->Form->create($loja, ['novalidate' => true]);
        // Destranco esses campos pois o cakephp na deixa trocar o valor de hidden fields
        $this->Form->unlockField('lat');
        $this->Form->unlockField('lng');
    ?>
        <div class="panel panel-default">
            <div class="panel-title">
                Dados da Loja
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $this->Form->input('nome') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default painel-setores">
            <div class="panel-title">
                Setores
            </div>
            <div class="panel-body">
                <div class="container-lojas">
                    <?= $this->Form->input('setores._ids', [
                        'multiple' => 'checkbox',
                        'label' => false,
                        'value' => ($loja->isNew()) ? array_keys($setores->toArray()) : null,
                        'templates' => [
                            'checkboxContainer' => '<div class="checkbox checkbox-success {{required}}">{{content}}</div>',
                        ]
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-title">
                Endereço
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('cep', [
                            'label' => 'CEP',
                            'append' => '<button type="button" data-url="' . $this->Url->build(['controller' => 'Enderecos', 'action' => 'todosPeloCep', 'prefix' => 'webservice_helpers', '_ext' => 'json']) . '" class="btn btn-default btn-preenche-endereco"><span class="fa fa-search"></span></button>',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('estados', [
                            'empty' => 'Seleciona o Estado:',
                            'label' => 'Estado',
                            'data-url' => $this->Url->build(['controller' => 'Cidades', 'action' => 'todasDoEstado', 'prefix' => 'webservice_helpers', 'grupo_slug' => null, '_ext' => 'json']),
                            'value' => (!$loja->isNew()) ? (int)$loja->cidade->estado_id : null, 'data-preselect-city' => (!$loja->isNew()) ? (int)$loja->cidade->id : '']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $this->Form->input('cidade_id', ['empty' => 'Selecione a cidade:']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('bairro') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $this->Form->input('endereco') ?>
                    </div>
                </div>
                <?php
                    echo $this->Form->input('lat', ['type' => 'hidden']);
                    echo $this->Form->input('lng', ['type' => 'hidden']);
                ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-title">
                Localização
            </div>
            <div class="panel-body">
                <input id="pac-input" class="form-control map-find-control dont-submit-form" type="text" placeholder="Buscar localização">
                <div class="<?= ($loja->errors('lat') || $loja->errors('lng')) ? 'border-error' : '' ?>">
                    <div id="map" style="width: 100%; height: 400px; display: block;"></div>
                </div>
                <p class="color10" style="margin-top: 5px; display: <?= ($loja->errors('lat') || $loja->errors('lng')) ? 'block' : 'none' ?>;">
                    Você não marcou a localidade.
                </p>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                    $options = ['type' => 'checkbox', 'hiddenField' => true];
                    if ($loja->isNew()) {
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