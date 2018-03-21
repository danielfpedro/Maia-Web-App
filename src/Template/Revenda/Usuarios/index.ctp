<?php
    $title = __('Usuários do Grupo "{0}"', $grupo->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Html->link('<span class="fa fa-plus"></span> Adicionar Usuário', ['action' => 'add', 'grupoId' => $this->request->grupoId], ['class' => 'btn btn-default btn-rounded btn-lg', 'escape' => false]);
    $this->end()
?>
 
<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
    	'Redes' => ['controller' => 'Grupos', 'action' => 'index'],
    ]
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
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome ou Email']) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->input('cargos', ['label' => 'Cargo', 'multiple' => 'multiple', 'class' => 'select2', 'value' => h($this->request->query('cargos')), 
                                'data-placeholder' => 'Todos']) ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-right" style="margin-top: 25px;">
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
                <div class="col-md-3">
                    Criado por
                </div>
                <div class="col-md-3">
                    Nome
                </div>
                <div class="col-md-2">
                    Cargo
                </div>
                <div class="col-md-2 text-center">
                    Status
                </div>
                <div class="col-md-2"></div>
            </div>
            <?php foreach ($usuarios as $usuario): ?>
                <div class="row table-grid">
                    <div class="col-sm-3 text-center-xs">
                        <?= ($usuario->quem_gravou) ? h($usuario->quem_gravou->short_name) : '-' ?>
                    </div>
                    <div class="col-sm-3 text-center-xs">
                    	<?= h($usuario->nome) ?>
                        <br>
                        <?= h($usuario->email) ?>
                    </div>
                    <div class="col-sm-2 text-center-xs">
                        <span class="label label-default">
                            <?= h($usuario->cargo->nome) ?>
                        </span>
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
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw-alt fa-fw"></span> Editar', ['action' => 'edit', 'usuarioId' => $usuario->id, 'grupoId' => $this->request->grupoId], ['escape' => false]) ?>   
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', ['action' => 'delete', 'usuarioId' => (int)$usuario->id, 'grupoId' => (int)$this->request->grupoId], ['escape' => false, 'confirm' => __('Você realmente deseja deletar o Usuário "{0}"?', h($usuario->nome))]) ?>
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
            
        </div>
    </div>
</div>
