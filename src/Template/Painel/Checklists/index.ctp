<?php
    $title = 'Questionários';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    echo $this->Html->script('Painel/checklists/index', ['block' => true]);
?>

<?php
    $this->start('breadcrumbButtonsRight');
?>
    <a href="#modal-importar-do-modelo" class="btn btn-light open-modal-importar-modelo" data-toggle="modal">Adicionar Questionário do Modelo</a>
<?php
    echo $this->Koletor->btnAdicionar('Adicionar Questionário');
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
                <div class="col-sm-2">
                    <?= $this->Paginator->sort('criado_em', 'Dt. Criação') ?>
                </div>
                <div class="col-sm-4">
                    <?= $this->Paginator->sort('nome', 'Nome') ?>
                </div>
                <?php
                    // IMPORTANTE
                    //
                    // AQUI se for 1 grupo modelo questionario eu mostro o segmento
                    // caso contrario eu mostro as visitas feitas
                ?>
                <div class="col-sm-3 text-left">
                    Geral
                </div>
                <div class="col-sm-1 text-center">
                    <?= $this->Paginator->sort('ativo', 'Status') ?>
                </div>
                <div class="col-sm-2 text-center"></div>
            </div>

            <?php foreach ($checklists as $checklist): ?>
                <div class="row table-grid">
                    <div class="col-sm-2">
                        <?= $checklist->criado_em->format('d/m/y') ?>
                    </div>
                    <div class="col-sm-4">
                        <?= h($checklist->nome) ?>
                        <br>
                        <dl>
                            <dt>Perguntas</dt>
                            <dd>
                                <span class="label label-default"><?= (int)$checklist->total_perguntas ?></span>    
                            </dd>
                            <dt>Grupos de Acesso</dt>
                            <dd>
                                <?php foreach ($checklist->grupos_de_acessos as $grupoDeAcesso): ?>
                                    <span class="label label-default"><?= h($grupoDeAcesso->nome) ?></span>
                                <?php endforeach ?>
                                <?php if (!$checklist->grupos_de_acessos): ?>
                                    -
                                <?php endif ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-3 text-left">
                        <dl>
                            <?php if ($grupoCustomizationData->id == 1): ?>
                                <dt>Segmento</dt>
                                <dd>
                                    <?php if ($checklist->_matchingData['Segmentos']['nome']): ?>
                                        <span class="label label-default">
                                            <?= $checklist->_matchingData['Segmentos']['nome'] ?>
                                        </span>
                                    <?php else: ?>
                                        <em>-</em>
                                    <?php endif; ?>
                                </dd>
                            <?php else: ?>
                                <dt>Visitas Feitas</dt>
                                <dd>
                                    <span class="label label-default">
                                        <?= $this->Html->link((int)$checklist->total_visitas_encerradas, ['controller' => 'Visitas', 'action' => 'index', '?' => ['encerramento' => 2, 'checklist' => (int)$checklist->id]], ['style' => 'color: #FFF']) ?>
                                    </span>
                                </dd>
                            <?php endif; ?>
                            <dt>Mínimo esperado</dt>
                            <dd>
                                <span class="label label-default">
                                    <?= (int)$checklist->minimo_esperado ?>%
                                </span>
                            </dd>
                            <dt>Permitir visita sem agendamento?</dt>
                            <dd>
                                <?php if ($checklist->sem_agendamento_flag): ?>
                                    <span class="label label-success">Sim</span>
                                <?php else: ?>
                                    <span class="label label-danger">Não</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-1 text-center">
                        <?= $this->Koletor->labelBoolean($checklist->ativo) ?>
                    </div>
                    <div class="col-sm-2 text-right">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <?= $this->Html->link(__('{0} Perguntas', '<span class="fa fa-comment"></span>', (int)$checklist->total_perguntas), ['action' => 'perguntasForm', 'checklistId' => (int)$checklist->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'checklistId' => (int)$checklist->id], ['escape' => false]) ?>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li class="<?= ($checklist->total_visitas_encerradas > 0) ? 'disabled' : '' ?>">
                                    <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', ['action' => 'delete', 'checklistId' => (int)$checklist->id], ['title' => ($checklist->total_visitas_encerradas > 0) ? 'A Checklist não pode ser removida pois uma ou mais visitas ligadas a ela já foram encerradas.' : '' ,'escape' => false, 'confirm' => __('Você realmente deseja deletar a checklist "{0}"?', h($checklist->nome))]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($checklists->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUMA CHECKLIST</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Checklists']) ?>
        </div>
    </div>
</div>

<?= $this->element('Painel/Checklists/modal_importar_do_modelo') ?>