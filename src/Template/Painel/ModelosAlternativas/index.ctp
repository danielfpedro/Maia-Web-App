<?php
    $title = 'Modelos de Alternativas';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Modelo');
    $this->end()
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => []
]) ?>

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
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome', 'autocomplete' => 'off']) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('status', ['options' => [1 => 'Ativos', 2 => 'Inativos'], 'empty' => 'Todos', 'value' => (int)$this->request->query('status')]) ?>
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
            <div class="row table-header-grid">
                <div class="col-sm-5">
                    Nome
                </div>
                <div class="col-sm-4">
                    Alternativas
                </div>
                <div class="col-sm-1 text-center">Status</div>
                <div class="col-sm-2 text-right"></div>
            </div>
            <?php foreach ($modelosAlternativas as $modeloAlternativa): ?>
                <div class="row table-grid">
                    <div class="col-sm-5"><?= h($modeloAlternativa->nome) ?></div>
                    <div class="col-sm-4">
                        <?php foreach ($modeloAlternativa->alternativas_dos_modelos as $alternativa): ?>
                        <span class="label label-default"><?= h($alternativa->alternativa) ?> (<?= (!is_null($alternativa->valor)) ? (int)$alternativa->valor : '-' ?>)</span>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-sm-1 text-center">
                        <?= $this->Koletor->labelBoolean($modeloAlternativa->ativo) ?>
                    </div>
                    <div class="col-sm-2 text-right">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'modelosAlternativaId' => (int)$modeloAlternativa->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', ['action' => 'delete', 'modelosAlternativaId' => (int)$modeloAlternativa->id], ['escape' => false, 'confirm' => __('Você realmente deseja deletar o modelo "{0}"?', h($modeloAlternativa->nome))]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($modelosAlternativas->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM MODELO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'ModelosAlternativas']) ?>
        </div>
    </div>
</div>
