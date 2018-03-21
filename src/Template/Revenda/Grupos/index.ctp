<?php
    $title = 'Grupos';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Grupo');
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
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome']) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('segmento_id', ['label' => 'Segmento', 'value' => (int)$this->request->query('segmento_id'), 'empty' => 'Todos']) ?>
                            </div>
                        </div>
                        <div class="col-md-5 text-right" style="margin-top: 25px;">
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
                <div class="col-md-2">
                    Criado por
                </div>
                <div class="col-md-4">
                    Nome
                </div>
                <div class="col-md-2">
                    Segmento
                </div>
                <div class="col-md-2 text-center">
                    Status
                </div>
                <div class="col-md-2"></div>
            </div>
            <?php foreach ($grupos as $grupo): ?>
                <div class="row table-grid">
                    <div class="col-sm-2 text-center-xs">
                        <?= ($grupo->quem_gravou) ? h($grupo->quem_gravou->short_name) : '-' ?>
                    </div>
                    <div class="col-sm-4 text-center-xs">
                        <?= $this->Html->link(h($grupo->nome), ['controller' => 'Usuarios', 'action' => 'login', 'prefix' => 'painel', 'grupo_slug' => $grupo->slug], ['target' => '_blank']) ?>
                        <br>
                        <span class="label label-default">
                            <?= (int)$grupo->total_lojas ?> Lojas(s)
                        </span>
                        <br>
                        
                        <?= $this->Html->link('<span class="label label-default" style="text-decoration: underline;">' . h($grupo->total_usuarios) . ' Usuários</span>', [
                            'controller' => 'Usuarios',
                            'action' => 'index',
                            'grupoId' => (int)$grupo->id
                        ], [
                            'escape' => false,
                        ]) ?>

                    </div>
                    <div class="col-sm-2 text-center-xs">
                        <span class="label label-default">
                            <?= h($grupo->segmento->nome) ?>
                        </span>
                    </div>
                    <div class="col-sm-2 text-center">
                        <?= $this->Koletor->labelBoolean($grupo->ativo) ?>
                    </div>
                    <div class="col-sm-2 text-right text-center-xs">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw fa-fw"></span> Editar', ['action' => 'edit', 'grupoId' => (int)$grupo->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Html->link('<span class="fa fa-laptop fa-fw"></span> Customização da Navbar', ['action' => 'edit', 'grupoId' => (int)$grupo->id, 'type' => 'navbar'], ['escape' => false]) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link('<span class="fa fa-tablet fa-fw"></span> Customização da Navbar do App', ['action' => 'edit', 'grupoId' => (int)$grupo->id, 'type' => 'appNavbar'], ['escape' => false]) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link('<span class="fa fa-user fa-fw"></span> Customização da tela de Login', ['action' => 'edit', 'grupoId' => (int)$grupo->id, 'type' => 'login'], ['escape' => false]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($grupos->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM GRUPO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Grupos']) ?>
        </div>
    </div>
</div>
