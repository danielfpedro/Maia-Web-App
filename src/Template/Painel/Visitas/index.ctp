<?php
    $title = 'Visitas';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Visita');
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('auditor', ['label' => 'Auditor', 'options' => $usuarios, 'empty' => 'Todos', 'value' => $this->request->query('auditor')]) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('questionario', ['label' => 'Questionário', 'options' => $checklists, 'empty' => 'Todas', 'value' => $this->request->query('questionario')]) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('loja', ['label' => 'Loja', 'options' => $lojas, 'empty' => 'Todas', 'value' => $this->request->query('loja')]) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('prazo', ['label' => 'Prazo', 'options' => [1 => 'Vencidos', 2 => 'No prazo'], 'empty' => 'Todos', 'value' => $this->request->query('prazo')]) ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <?php echo $this->Form->input('encerramento', ['options' => [1 => 'Pendentes', 2 => 'Encerradas'], 'empty' => 'Todos', 'value' => $this->request->query('encerramento')]) ?>
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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="#filtro-extra" class="filtro-extra-toggle"><span class="fa fa-chevron-down"></span> Mais Filtros</a>
                            </div>
                        </div>
                    </div>

                    <div class="row filtro-extra">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prazo-de">Dt. Prazo</label>
                                <input type="text" id="prazo-de" name="prazo_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-ate" name="prazo_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_ate')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="encerramento-de">Dt. do Encerramento</label>
                                <input type="text" id="encerramento-de" name="encerramento_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('encerramento_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="encerramento-ate" name="encerramento_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('encerramento_ate')) ?>">
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
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('usuario_id', 'Audit. Resp.') ?> / <?= $this->Paginator->sort('usuario_vinculado_id', 'Audit. Vinc.') ?>
                </div>
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('loja_id') ?>&nbsp;/&nbsp;
                    <?= $this->Paginator->sort('checklist_id', 'Questionário') ?>
                </div>
                <div class="col-sm-3 text-left">
                    <?= $this->Paginator->sort('prazo', 'Prazo') ?>
                    &nbsp;/&nbsp;
                    <?= $this->Paginator->sort('dt_encerramento', 'Encerramento') ?>
                </div>
                <div class="col-sm-1 text-center">
                    <?= $this->Paginator->sort('ativo', 'Status') ?>
                </div>
                <div class="col-sm-2 text-right"></div>
            </div>

            <?php foreach ($visitas as $visita): ?>
                <div class="row table-grid">
                    <div class="col-sm-3">
                        <dl>
                            <dt>Cod.</dt>
                            <dd>
                                <?= h($visita->cod) ?>
                            </dd>
                            <dt>Criado por</dt>
                            <dd>
                                <?= ($visita->quem_gravou) ? h($visita->quem_gravou->short_name) : '-' ?> em <?= $visita->criado_em->format('d/m/Y') ?>
                            </dd>
                            <dt>Auditor Responsável</dt>
                            <dd>
                                <?= h($visita->usuario->nome) ?>
                            </dd>
                            <dt>Teve Agendamento?</dt>
                            <dd>
                                <?php if ($visita->teve_agendamento_flag): ?>
                                    <span class="label label-success">Sim</span>
                                <?php else: ?>
                                    <span class="label label-danger">Não</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-3">
                        <?php
                            $totalRespostasCriticas = count($visita->respostas);
                            $totalRespostasCriticasResolvidas = 0;
                            foreach ($visita->respostas as $resposta) {
                                if ($resposta->critico_resolvido) {
                                    $totalRespostasCriticasResolvidas++;
                                }
                            }
                        ?>
                        <dl>
                            <dt>Loja</dt>
                            <dd>
                                <?= h($visita->loja->nome) ?>
                            </dd>
                            <dt>Requerimento de Localização</dt>
                            <dd>
                                <?= h($visita->getRequerimentoLocalizacao()) ?>
                            </dd>

                            <dt>Questionário</dt>
                            <dd><?= h($visita->_matchingData['Checklists']['nome']) ?></dd>
                        </dl>
                    </div>
                    <div class="col-sm-3 text-left">
                        <dl>
                            <dt>Prazo</dt>
                            <dd>
                                <?php if ($visita->prazo): ?>
                                    <?php
                                        if ($visita->prazo->format('Y-m-d') < (new \Datetime())->format('Y-m-d')) {
                                            $label = 'danger';
                                            $descricao = 'Vencido ';
                                            $color = 'color10';
                                        } else {
                                            $label = 'success';
                                            $descricao = 'Vence ';
                                            $color = 'color7';
                                        }
                                        if ($visita->dt_encerramento) {
                                            $label = 'default';
                                            $descricao = '';
                                            $color = '';
                                        }
                                    ?>
                                    <span class="label label-<?= $label ?>"><?= $visita->prazo->format('d/m/y') ?></span>
                                    <?php if ($descricao): ?>
                                        <br>
                                        <span class="<?= $color ?>"><?= $descricao . $visita->prazo->timeAgoInWords(['accuracy' => 'day']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </dd>

                            <dt>Encerramento</dt>
                            <dd>
                                <?php if (!$visita->dt_encerramento): ?>
                                    <span class="label label-danger">Pendente</span>
                                <?php else: ?>
                                    <span class="label label-default">
                                        <?= $visita->dt_encerramento->format('d/m/Y \à\s H:i') ?>
                                    </span>
                                    <div style="margin-top: 8px;">
                                        <?= $this->Html->link('Resultado Público', $visita->getUrlPublicaDoResultado(), ['class' => 'krazy-external-link']) ?>
                                    </div>
                                <?php endif; ?>
                            </dd>
                        </dl>

                    </div>
                    <div class="col-sm-1 text-center">
                        <?= $this->Koletor->labelBoolean($visita->ativo) ?>
                    </div>
                    <div class="col-sm-2 text-right">
                        <div class="dropdown">
                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                <span class="fa fa-cog"></span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="<?= (!$visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa-check fa-fw"></span> Visualizar Resultado', ['controller' => 'Visitas', 'action' => 'resultado', 'visitaId' => (int)$visita->id], ['title' => (!$visita->dt_encerramento) ? 'A Visita ainda não possui resultados para mostrar.' : '', 'escape' => false]) ?>
                                </li>
                                
                                <li role="separator" class="divider"></li>

                                <li class="<?= (!$visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa-check fa-bolt fa-fw"></span> Visualizar Planos de Ação <span class="badge pull-right"> ' . (int)$visita->total_planos_taticos . '</span>', ['controller' => 'PlanosTaticos', 'action' => 'index', '?' => ['visita' => h($visita->cod)]], ['escape' => false]) ?>
                                </li>
                                <li class="<?= (!$visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa-check fa-bolt fa-fw"></span> Criar Planos de Ação', ['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'index', '?' => ['visita' => h($visita->cod)]], ['escape' => false]) ?>
                                </li>

                                <li role="separator" class="divider"></li>

                                <li class="<?= ($visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw fa-fw"></span> Editar', ['action' => 'edit', 'visitaId' => (int)$visita->id], ['escape' => false]) ?>
                                </li>
                                <li class="<?= ($visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa-at fa-fw"></span> Editar Notificações por Email', ['action' => 'editNotificacoesPorEmail', 'visitaId' => (int)$visita->id], ['escape' => false]) ?>
                                </li>
                                
                                <li class="<?= ($visita->dt_encerramento) ? 'disabled' : '' ?>">
                                    <?= $this->Html->link('<span class="fa fa-bolt fa-fw"></span> Editar Planos de Ação Automáticos', ['action' => 'editPlanosTaticosPreInfos', 'visitaId' => (int)$visita->id], ['escape' => false]) ?>
                                </li>

                                <li role="separator" class="divider"></li>
                                <li class="">
                                    <?= $this->Html->link('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', [
                                        'action' => 'delete',
                                        'visitaId' => (int)$visita->id],
                                        [
                                            'class' => ($visita->dt_encerramento) ? 'btn-critical-delete' : 'btn-delete',
                                            'data-encerrada' => ($visita->dt_encerramento),
                                            'title' => ($visita->dt_encerramento) ? 'A Visita não pode ser removida pois já foi encerrada.': '',
                                            'escape' => false,
                                        ]) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($visitas->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUMA VISITA</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Visitas']) ?>
        </div>
    </div>
</div>
