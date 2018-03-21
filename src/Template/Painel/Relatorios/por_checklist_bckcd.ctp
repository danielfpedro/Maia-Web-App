<?php
    $title = 'Relatório por Checklist';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
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
                                <?php echo $this->Form->input('checklist', ['label' => 'Checklist', 'options' => $checklists, 'class' => '', 'empty' => 'Selecione a Checklist:', 'value' => $this->request->query('checklist')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('auditor', ['label' => 'Auditor', 'options' => $usuarios, 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('auditor')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('loja', ['label' => 'Loja', 'options' => $lojas, 'data-placeholder' => 'Todas', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('loja')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('auditor_supervisionado', ['label' => 'Auditor Supervisionado', 'options' => $usuarios, 'empty' => 'Todos', 'value' => $this->request->query('auditor_supervisionado')]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Prazo</label>
                                <input type="text" id="prazo-de" name="prazo_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-ate" name="prazo_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_ate')) ?>">
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

<?php if ($checklist): ?>
    <div class="row">
        <div class="col-md-6">
            <h4>
                <i class="fa fa-file-text-o"></i>&nbsp;&nbsp;<?= $checklist->nome ?>
                <?php if ($auditor): ?>
                    &nbsp;/&nbsp;<?= $auditor->short_name ?>
                <?php endif; ?>
                <?php if ($auditorSupervisionado): ?>
                    &nbsp;/&nbsp; Supervisão <?= $auditorSupervisionado->short_name ?>
                <?php endif; ?>
            </h4>
        </div>
    </div>

    <?php // Mesmo tendo filtor pode não ter visitas para mostrar entao eu teste aqui ?>

    <?php if ($visitas): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="table-responsive">
                        <table class="table table-stripped table-hover table-condensed">
                            <thead>
                                <tr style="width: 300px;">
                                    <th>Itens Avaliados</th>
                                    <!-- LOOPS LOJAS -->
                                    <?php foreach ($visitas as $visita): ?>
                                        <th class="text-center"  style="width: 150px;">
                                            <?= $this->Html->link($visita->loja->nome, ['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'view', 'visitaId' => (int)$visita->id], ['target' => '_blank']) ?>
                                            <br>
                                            <span style="text-transform: none; font-weight: normal;" class="text-muted"><?= $visita->usuario->short_name ?></span>
                                            <br>
                                            <small style="text-transform: lowercase; font-weight: normal;" class="text-muted"><?= $visita->dt_encerramento->format('d/m/y \à\s H:i') ?></small>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-center"  style="width: 150px;">Média</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $visitaIterarPerguntas = $visitas->first();
                                    // $visitaIterarPerguntas->ordenaSetores();
                                    // $visitaIterarPerguntas->setPerguntasNosSetores();
                                    // dd($visitaIterarPerguntas->checklist->setores);
                                    $iteracaoTodosPerguntas = 1;
                                ?>
                                <?php foreach ($visitaIterarPerguntas->getSetoresOrdenados() as $i => $setor): ?>
                                    <?php foreach ($setor->perguntas as $perguntaIndex => $pergunta): ?>
                                        <tr>
                                            <td style="width: 300px;">
                                                <?= $iteracaoTodosPerguntas ?>) <?= $pergunta->pergunta ?>
                                            </td>
                                            <?php foreach ($visitas as $visita): ?>
                                                <?php
                                                    $visita->ordenaSetores();
                                                    //$visita->setPerguntasNosSetores();
                                                ?>
                                                <td>
                                                    -
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php $iteracaoTodosPerguntas++ ?>
                                    <?php endforeach; ?>
                                    <!-- Fim iteração setor -->
                                    <!-- Inicio linha mostra resultados do setor -->
                                    <tr>
                                        <td>
                                            Atingimento do setor <span><?= $setor->nome ?></span>
                                        </td>
                                        <!-- Iteração resultado de cada visita -->
                                        <?php foreach ($visitas as $visita): ?>
                                            <td>
                                                -
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>



                                <tr class="color3-bg">
                                    <td class="text-center">
                                        <strong>Atingimento Geral</strong>
                                    </td>
                                    <?php $somaAtingimento = 0 ?>
                                    <?php $somaAtingimentoPorcentagem = 0 ?>
                                    <?php foreach ($visitas as $visita): ?>
                                        <td class="text-center color<?= ($visita->atingimento['diferenca'] >= 0) ? '7' : '10' ?>" >
                                            <?php
                                                $atingido = $visita->atingimento['atingido'];
                                                $somaAtingimento += $atingido;
                                            ?>
                                            <strong><?= number_format($atingido, 2) ?></strong> / <strong><?= number_format($visita->atingimento['maximo_possivel'], 2) ?></strong>
                                            &nbsp;|&nbsp;
                                            <strong><?= round($visita->atingimento['atingido_porcentagem']) ?>%</strong>
                                            <small>
                                                (
                                                <?= $visita->checklist->minimo_esperado ?>%
                                                <span class="fa fa-caret-<?= ($visita->atingimento['diferenca'] >= 0) ? 'up' : 'down' ?>"></span> <?= round(abs($visita->atingimento['diferenca'])) ?>
                                                )
                                            </small>
                                        </td>
                                        <?php $somaAtingimentoPorcentagem += $visita->atingimento['atingido_porcentagem'] ?>
                                    <?php endforeach ?>

                                    <?php
                                        $mediaPorcentagem = $somaAtingimentoPorcentagem / count($visitas);
                                        $diferencaPorcentagem = $mediaPorcentagem - $visita->checklist['minimo_esperado'];
                                    ?>
                                    <td class="text-center color<?= ($diferencaPorcentagem >= 0) ? '7' : '10' ?>" >
                                        <strong><?= number_format($somaAtingimento, 2) ?> / <?= number_format($visita->atingimento['maximo_possivel'], 2) ?></strong>
                                        &nbsp;|&nbsp;
                                        <strong><?= round($mediaPorcentagem) ?>%</strong>
                                        <small>
                                            (
                                            <?= $visita->checklist->minimo_esperado ?>%
                                            <span class="fa fa-caret-<?= ($diferencaPorcentagem >= 0) ? 'up' : 'down' ?>"></span> <?= round(abs($diferencaPorcentagem)) ?>
                                            )
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <p style="margin-top: 10px;"><strong style="text-transform: uppercase">Nenhum resultado</strong> para mostrar.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <p style="margin-top: 10px;">Selecione algum filtro para <strong style="text-transform: uppercase">gerar o relatório</strong>.</p>
            </div>
        </div>
    </div>
<?php endif; ?>
