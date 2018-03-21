<?php
    $title = 'Usuários';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Usuários');
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
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome ou Email']) ?>
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

            <div class="row table-header-grid hidden-xs hidden-sm">
                <div class="col-md-8">
                    Nome
                </div>
                <div class="col-md-2 text-center">
                    Status
                </div>
                <div class="col-md-2"></div>
            </div>
            <?php foreach ($usuarios as $usuario): ?>
                <div class="row table-grid">
                    <div class="col-sm-8">
                        <?= h($usuario->nome) ?>
                        <br>
                        <?= h($usuario->email) ?>
                    </div>
                    <div class="col-sm-2 text-center">
                        <?= $this->Koletor->labelBoolean($usuario->ativo) ?>
                    </div>
                    <div class="col-sm-2 text-right text-center-xs">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw fa-fw"></span> Editar', ['action' => 'edit', 'usuarioId' => (int)$usuario->id], ['escape' => false]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($usuarios->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM USUÁRIO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Usuarios']) ?>
        </div>
    </div>
</div>
