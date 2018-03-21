<?php
    $title = __('Editar informações do Plano Tático "{0} / {1}"', $visita->loja->nome, $visita->usuario->short_name);
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
                        <div class="col-md-12">
                        <?php
                            $options = [
                                'type' => 'checkbox',
                                'hiddenField' => true,
                                'checked' => $visita->planos_taticos_pre_info,
                                'class' => 'toggle-target-from-checkbox',
                                'data-target' => 'planos-taticos-pre-info-container',
                                'label' => 'Gerar Planos de Ação automaticamente?',
                                'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']];
                            echo $this->Form->input('planos_taticos_pre_info.flag', $options);
                        ?>                            
                        </div>
                    </div>
                    <div class="planos-taticos-pre-info-container" style="<?= (!$visita->planos_taticos_pre_info) ? 'display: none;' : ''; ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $this->Form->input('planos_taticos_pre_info.solicitante_id', ['type' => 'select', 'empty' => 'Selecione:', 'label' => 'Solcitante', 'options' => $solicitantes]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $this->Form->input('planos_taticos_pre_info.who_id', ['type' => 'select', 'empty' => 'Selecione:', 'label' => 'Executante', 'options' => $executantes]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $this->Form->input('planos_taticos_pre_info.prazo_dias', ['type' => 'number', 'label' => 'Prazo (Em dias)']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="fa fa-warning"></span> O <strong>PRAZO</strong> para concluir o plano de ação será baseado na quantidade de dias do campo <strong>PRAZO DIAS</strong> a partir do encerramento da visita.
                            </div>
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
