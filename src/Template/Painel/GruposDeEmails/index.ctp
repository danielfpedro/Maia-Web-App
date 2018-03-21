<?php
    $title = 'Grupos de Emails';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Grupo de Emails');
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('questionarios', [
                                    'label' => 'Questionários',
                                    'data-placeholder' => 'Todos',
                                    'value' => h($this->request->query('questionarios')),
                                    'placeholder' => 'Questionário',
                                    'class' => 'select2',
                                    'multiple' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('lojas', [
                                    'label' => 'Lojas',
                                    'data-placeholder' => 'Todas',
                                    'value' => h($this->request->query('lojas')),
                                    'placeholder' => 'Lojas',
                                    'class' => 'select2',
                                    'multiple' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-2 text-right" style="margin-top: 25px;">
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
                <div class="col-sm-2">
                    Nome
                </div>
                <div class="col-sm-2">
                    Lojas vinculadas
                </div>
                <div class="col-sm-2">
                    Questionário Vinculado
                </div>
                <div class="col-sm-4">
                    Emails
                </div>
                <div class="col-sm-2"></div>
            </div>

            <?php foreach ($gruposDeEmails as $grupo): ?>

                <div class="row table-grid">
                    <div class="col-sm-2">
                        <strong><?= h($grupo->nome) ?></strong>
                    </div>
                    <div class="col-sm-2">
                        <?php foreach ($grupo->lojas as $loja): ?>
                            <span class="label label-default label-xs"><?= h($loja->nome) ?></span>
                        <?php endforeach ?>
                        <?php if (!$grupo->lojas): ?>
                            <em>Todas</em>
                        <?php endif ?>
                    </div>
                    <div class="col-sm-2">
                        <?php foreach ($grupo->checklists as $checklist): ?>
                            <span class="label label-default label-xs"><?= h($checklist->nome) ?></span>
                        <?php endforeach ?>
                        <?php if (!$grupo->checklists): ?>
                            <em>Todos</em>
                        <?php endif ?>
                    </div>
                    <div class="col-sm-4">
                        <dl>
                            <dt>Emails Resultados</dt>
                            <dd>
                                <?php foreach ($grupo->emails_resultados_as_array as $email): ?>
                                    <span class="label label-default label-xs"><?= h($email) ?></span>
                                <?php endforeach ?>                                
                                <?php if (!$grupo->emails_resultados_as_array): ?>
                                    <em>-</em>        
                                <?php endif ?>    
                            </dd>
                            <dt>Emails Críticos</dt>
                            <dd>
                                <?php foreach ($grupo->emails_criticos_as_array as $email): ?>
                                    <span class="label label-default label-xs"><?= h($email) ?></span>
                                <?php endforeach ?>        
                                <?php if (!$grupo->emails_criticos_as_array): ?>
                                    <em>-</em>        
                                <?php endif ?>    
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-2 text-right">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'gruposDeEmailId' => $grupo->id], ['escape' => false]) ?>   
                                </li>
                                <li role="separator" class="divider"></li>
                                <li class="">
                                    <?= $this->Form->postLink(
                                        '<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>',
                                        [
                                            'action' => 'delete',
                                            'gruposDeEmailId' => $grupo->id
                                        ],
                                        [
                                            'confirm' => 'Você realmente deseja deletar este Grupo de Emails?',
                                            'escape' => false
                                        ])
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($gruposDeEmails->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM GRUPO DE EMAIL</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'GruposDeEmails']) ?>
        </div>
    </div>
</div>
