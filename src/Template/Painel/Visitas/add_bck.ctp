<?php
    $title = 'Adicionar Visita';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('Painel/visitas', ['block' => true]);
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
                <div class="panel-title">
                    Dados da Visita
                </div>
                <div class="panel-body">
                    <div class="form-group <?= (isset($visita->errors()['usuario_id']) && $visita->errors()['usuario_id']) ? 'has-error' : '' ?>">
                        <label for="usuario-id" class="control-label col-md-2">Responsável</label>
                        <div class="col-md-4">
                            <select class="form-control" name="usuario_id" id="usuario-id" required>
                                <option value="">Selecione o Responsável:</option>
                                <?php foreach ($usuarios as $key => $usuario): ?>
                                    <option value="<?= (int)$usuario->id ?>" data-lojas="<?= str_replace('"', "&quot;", json_encode($usuario->lojas)) ?>" <?= ((int)$usuario->id == (int)$visita->usuario_id) ? 'selected' : '' ?>>
                                        <?= h($usuario->nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <span class="col-md-6 help-block">
                            <?= (isset($visita->errors()['usuario_id']) && $visita->errors()['usuario_id']) ? $this->Text->toList($visita->errors()['usuario_id']) : '' ?>
                        </span>
                    </div>
                    <?php
                        echo $this->Form->input('checklist_id', ['empty' => 'Selecione a Checklist:']);
                    ?>
                    <?php $this->Form->setConfig('columns', ['label' => 2, 'input' => 2, 'error' => 0]) ?>
                    <?= $this->Form->input('prazo', ['type' => 'text', 'class' => 'date']) ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-title">
                    Lojas
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-2">
                            <div class="container-lojas <?= (isset($visita->errors()['lojas']) && $visita->errors()['lojas']) ? 'color10' : '' ?>">
                                <?php foreach ($lojas as $key => $loja): ?>
                                    <div class="checkbox">
                                        <input type="checkbox" name="lojas[]" value="<?= (int)$loja->id ?>" id="loja-<?= (int)$loja->id ?>">
                                        <label for="loja-<?= (int)$loja->id ?>">
                                            <?= $loja->nome ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <span class="col-md-4 help-block <?= (isset($visita->errors()['lojas']) && $visita->errors()['lojas']) ? 'color10' : '' ?>">
                            <?= (isset($visita->errors()['lojas']) && $visita->errors()['lojas']) ? $this->Text->toList($visita->errors()['lojas']) : '' ?>
                        </span>
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
