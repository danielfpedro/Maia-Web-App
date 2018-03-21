<?php
    // debug($usuario);
    $title = ($concorrencia->isNew()) ? 'Nova concorrência' : sprintf('Editar Concorrência "%s"', $concorrencia->descricao) ;
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrências' => $this->request->referer(),
    ]
]) ?>

<div class="row">
    <div class="col-md-12">
        <?= $this->Form->create($concorrencia, [
            'horizontal' => true,
            'novalidate' => true,
            'columns' => [
                'md' => [
                    'label' => 2,
                    'input' => 4,
                    'error' => 6
                ]
            ]
        ]); ?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados da concorrência
                </div>
                <div class="panel-body">
                    <?php if (!$concorrencia->isNew()): ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">#</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <em>
                                        <?= h($concorrencia->identificacao) ?>
                                    </em>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                        echo $this->Form->input('descricao', ['label' => 'Descrição', 'type' => 'textarea']);
                        echo $this->Form->input('loja_id', ['empty' => 'Selecione a loja:']);
                    ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body text-right">
                    <button type="submit" class="btn btn-default">Salvar</button>
                </div>
            </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
