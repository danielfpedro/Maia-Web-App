<?php
    $title = 'Logs';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => []
]) ?>
<!-- Panel Pesquisa -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-title">
                Filtros
            </div>
            <div class="panel-body">
                <form class="" action="" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->input('autor', [
                                    'options' => $autores,
                                    'empty' => 'Todos',
                                    'value' => $this->request->query('autor')
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->input('modulo', [
                                    'label' => 'Módulo',
                                    'options' => $modulos,
                                    'empty' => 'Todos',
                                    'value' => $this->request->query('modulo')
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->input('tipo', [
                                    'label' => 'Tipo',
                                    'options' => $logsTipos,
                                    'empty' => 'Todos',
                                    'value' => $this->request->query('tipo')
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="intervalo-de">Intervalo de Data</label>
                                <input type="text" id="intervalo-de" name="intervalo_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('intervalo_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="intervalo-ate" name="intervalo_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('intervalo_ate')) ?>">
                            </div>
                        </div>
                        <div class="col-md-6 text-right" style="margin-top: 25px;">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">
                                    <span class="fa fa-search"></span> Pesquisar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">

            <div class="row table-header-grid hidden-xs hidden-sm">
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('autor', 'Autor') ?>
                    &nbsp;/&nbsp;
                    <?= $this->Paginator->sort('criado_em', 'Data') ?>
                </div>
                <div class="col-sm-2">
                    <?= $this->Paginator->sort('modulo_id', 'Módulo') ?>
                </div>
                <div class="col-sm-2">
                    <?= $this->Paginator->sort('logs_tipo_id', 'Tipo') ?>
                </div>
                <div class="col-sm-5">
                    Descrição
                </div>
            </div>
            <?php foreach ($logs as $log): ?>
                <div class="row table-grid">
                    <div class="col-sm-3">
                        <?= h($log->autor->short_name) ?> em <strong><?= $log->criado_em->format('d/m/y H:i:s') ?></strong>
                    </div>
                    <div class="col-sm-2">
                        <?= h($log->modulo->nome) ?>
                    </div>
                    <div class="col-sm-2">
                        <span class="<?= h($log->logs_tipo->icon) ?>"></span> <?= h($log->logs_tipo->nome) ?>
                    </div>
                    <div class="col-sm-5">
                        <?= ($log->descricao) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($logs->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM LOG</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Logs']) ?>
        </div>
    </div>
</div>
