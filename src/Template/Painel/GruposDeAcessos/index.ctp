<?php
    $title = 'Grupos de Acessos';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Grupo de Acesso');
    $this->end()
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
                                <?= $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome']) ?>
                            </div>
                        </div>
                        <div class="col-md-8 text-right" style="margin-top: 25px;">
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
                <div class="col-sm-9">
                    <?= $this->Paginator->sort('nome')  ?>
                </div>
                <div class="col-sm-3"></div>
            </div>

            <?php foreach ($gruposDeAcessos as $grupoDeAcesso): ?>
                <div class="row table-grid">
                    <div class="col-sm-9"><?= h($grupoDeAcesso->nome) ?></div>
                    <div class="col-sm-3 text-right">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'grupoDeAcessoId' => (int)$grupoDeAcesso->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', ['action' => 'delete', 'grupoDeAcessoId' => (int)$grupoDeAcesso->id], ['escape' => false, 'confirm' => __('Você realmente deseja deletar o Setor "{0}"?', h($grupoDeAcesso->nome))]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
            <?php if ($gruposDeAcessos->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM GRUPO DE ACESSO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'GruposDeAcessos']) ?>
        </div>
    </div>
</div>
