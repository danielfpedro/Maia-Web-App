<?php
    $title = 'Lojas';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Loja');
    $this->end()
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => []
]) ?>

<div class="main-panels-container main-panels-large">
    <div class="panel panel-default">
        <div class="panel-title">
            Filtros
        </div>
        <div class="panel-body">
            <form class="" action="" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo $this->Form->input('q', ['label' => 'Nome ou Endereço', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo $this->Form->input('setores', ['class' => 'select2', 'multiple' => true, 'data-placeholder' => 'Todos', 'value' => h($this->request->query('setores'))]) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo $this->Form->input('status', ['options' => [true => 'Ativos', false => 'Inativos'], 'empty' => 'Todos', 'value' => h($this->request->query('status'))]) ?>
                        </div>
                    </div>
                    <div class="col-md-3 text-right btn-pesquisar-container">
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

<div class="main-panels-container main-panels-large">
    <div class="panel panel-default">

        <div class="row table-header-grid">
            <div class="col-sm-8">
                <?= $this->Sorter->sort('Lojas.nome', 'Nome') ?>
            </div>
            <div class="col-md-2 text-right">
                <?= $this->Paginator->sort('ativo') ?>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <?php foreach ($lojas as $loja): ?>

            <div class="row table-grid">
                <div class="col-sm-8">
                    <strong><?= h($loja->nome) ?></strong>
                    <p>
                        <?= h($loja->endereco) ?>, <?= h($loja->bairro) ?>, <?= h($loja->cidade->nome) ?> / <?= h($loja->cidade->uf) ?>    
                    </p>
                    <?php foreach ($loja->setores as $setor): ?>
                        <span class="label label-default label-xs"><?= h($setor->nome) ?></span>
                    <?php endforeach ?>

                    <?php if (!$loja->setores): ?>
                        <p><em>Esta loja não possui nenhum setor.</em></p>
                    <?php endif ?>
                </div>
                <div class="col-sm-2 text-right">
                    <?= $this->Koletor->labelBoolean($loja->ativo) ?>
                </div>
                <div class="col-sm-2 text-right">
                    <div class="dropdown">
                        <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="fa fa-cog"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'lojaId' => (int)$loja->id], ['escape' => false]) ?>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <?= $this->Html->link('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', '/setor/deletar',
                                    [
                                        'escape' => false,
                                        'class' => 'krazy-btn-delete',
                                        
                                        'krazy-url' => $this->Url->build([
                                            'controller' => 'Lojas',
                                            'action' => 'delete',
                                            'lojaId' => (int)$loja->id,
                                            '_ext' => 'json',
                                            'method' => 'delete'
                                        ]),
                                        'krazy-message' => 'Você realmente deseja deletar a Loja <strong>\'' .h($loja->nome). '\'</strong>?',

                                        'data-_csrf-token' => $this->request->_csrfToken,
                                    ])
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if ($lojas->isEmpty()): ?>
            <div class="row table-grid">
                <div class="col-sm-12">
                    <strong>NENHUMA LOJA</strong> para mostrar.
                </div>
            </div>
        <?php endif; ?>
    <!-- Paginação -->
    <div class="text-right">
        <?= $this->element('Painel/paginator', ['model' => 'Lojas']) ?>
    </div>
</div>
