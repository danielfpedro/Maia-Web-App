<?php
    $title = 'Usuários';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Usuário');
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome ou Email']) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('cargos', ['label' => 'Cargos', 'multiple' => 'multiple', 'class' => 'select2', 'options' => $cargos, 'data-placeholder' => 'Todos', 'value' => (int)$this->request->query('cargos')]) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('loja', ['label' => 'Lojas', 'options' => $lojas, 'empty' => 'Todas', 'value' => (int)$this->request->query('loja')]) ?>
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
                <div class="col-md-3">
                    Email
                </div>
                <div class="col-md-4">
                    Cargo / Lojas Atribuídas
                </div>
                <div class="col-md-1 text-center">
                    Status
                </div>
                <div class="col-md-2"></div>
            </div>
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                    $souEu = false;
                    if ($usuario->id == (int)$this->request->session()->read('Auth.User.id')) {
                        $souEu = true;
                    }
                ?>
                <div class="row table-grid">
                    <div class="col-sm-2 text-center-xs">
                        <?= ($usuario->quem_gravou) ? h($usuario->quem_gravou->short_name) : '-' ?>
                    </div>
                    <div class="col-sm-3 text-center-xs">
                        <?= ($souEu) ? '<small><span class="label label-success">Você</span></small>' : '' ?>&nbsp;<?= h($usuario->nome) ?>
                        <br>
                        <?= h($usuario->email) ?>
                    </div>
                    <div class="col-sm-4 text-center-xs">
                        <dl>
                            <dt>Cargo</dt>
                            <dd>
                                <?php foreach ($usuario->cargos as $cargo): ?>
                                    <span class="label label-default"><?= h($cargo->nome) ?></span>
                                <?php endforeach; ?>
                            </dd>

                            <dt>Grupos de Acesso</dt>
                            <dd>
                                <?php foreach ($usuario->grupos_de_acessos as $grupoDeAcesso): ?>
                                    <span class="label label-default"><?= h($grupoDeAcesso->nome) ?></span>
                                <?php endforeach; ?>

                                <?php if (!$usuario->grupos_de_acessos): ?>
                                    <em>-</em>
                                <?php endif; ?>
                            </dd>

                            <dt>Lojas Atribuídas</dt>
                            <dd>
                                <?php if (count($usuario->lojas) > 0): ?>
                                    <?php foreach ($usuario->lojas as $loja): ?>
                                        <span class="label label-default"><?= h($loja->nome) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <em>-</em>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-1 text-center">
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
                                    <?= $this->Html->link('<span class="fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'usuarioId' => (int)$usuario->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', ['action' => 'delete', 'usuarioId' => (int)$usuario->id], ['escape' => false, 'confirm' => __('Você realmente deseja deletar o usuário "{0}"?', h($usuario->nome))]) ?>
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
