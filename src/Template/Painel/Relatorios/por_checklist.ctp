<?php
    $title = 'Relatório por Questionário';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('Painel/relatorios/por_checklist',  ['block' => true]);
    $this->Html->script('Painel/interacao_filtros_checklist_setores_perguntas',  ['block' => true]);

    $this->Html->script('Painel/table-content-fixed-width',  ['block' => true]);
    $this->Html->script('../lib/preenche_cidades/dist/plugin',  ['block' => true]);
    $this->Html->script('https://www.gstatic.com/charts/loader.js',  ['block' => true]);

    $this->Html->scriptStart(['block' => true]);
        echo "google.charts.load('current', {packages: ['corechart']});";

        echo "
            var dadosDoGrafico = $.parseJSON($('#dados-do-grafico').val());
            if (dadosDoGrafico.length > 0) {
                google.charts.setOnLoadCallback(drawChart);
            }
        ";

        echo "
            function drawChart() {
              var data = google.visualization.arrayToDataTable(dadosDoGrafico);

              var options = {
                 interpolateNulls: true,
                 pointSize: 5,

                curveType: 'none',
                chartArea: {
                    width: 'auto',
                    height: 'auto'
                },
                animation: {
                    startup: true
                },
                legend: { position: 'bottom' },
                hAxis: {
                    gridlines: {
                        count: 10
                    },
                },
                vAxis: {
                    gridlines: {
                        count: 10
                    },
                }
              };

              var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

              chart.draw(data, options);
            }
        ";
    $this->Html->scriptEnd();
?>
<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => []
]) ?>

<textarea name="name" id="dados-do-grafico" rows="8" cols="80" style="display: none"><?= json_encode($dadosDoGrafico) ?></textarea>

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
                                <?php echo $this->Form->input('checklist', [
                                    'label' => 'Questionário',
                                    'options' => $checklistsCombo,
                                    'class' => '',
                                    'empty' => 'Selecione o Questionário:',
                                    'value' => $this->request->query('checklist'),
                                    'data-url-carrega-setores' => $this->Url->build(['controller' => 'Checklists', 'action' => 'getSetoresParaCombo', 'checklistId' => ':checklistId', '_ext' => 'json']),
                                    'data-url-carrega-perguntas' => $this->Url->build(['controller' => 'Checklists', 'action' => 'getPerguntasParaCombo', 'checklistId' => ':checklistId', '_ext' => 'json']),
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('setores', [
                                    'type' => 'select',
                                    'class' => 'select2',
                                    'multiple' => 'multiple',
                                    'data-value' => null,
                                    'style' => 'width: 100%;']) ?>
                            </div>
                        </div>                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('perguntas', [
                                    'label' => 'Perguntas',
                                    'type' => 'select',
                                    'class' => 'select2',
                                    'multiple' => 'multiple',
                                    'style' => 'width: 100%;',
                                    'data-value' => $this->request->query('perguntas')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('lojas', ['label' => 'Lojas', 'options' => $lojas, 'data-placeholder' => 'Todas', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('lojas')]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('auditores', ['label' => 'Auditores', 'options' => $usuarios, 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('auditores')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prazo-de">Dt. Encerramento</label>
                                <input type="text" id="prazo-de" name="prazo_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-ate" name="prazo_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_ate')) ?>">
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

<?php
// Aqui a gente mostra quais filtros foram selecionados para ele ter noç
// pq os dados estão daquele jeito.. mas se nao tem  dados foda-se entao nem mostro
// pq isso tb teste $visitas
?>
<?php if ($checklist && $visitas): ?>
    <div class="row">
        <div class="col-md-6">
            <h5>
                <i class="fa fa-file-alt"></i>&nbsp;&nbsp;<?= $checklist->nome ?>
                <?php if ($this->request->query('prazo_de')): ?>
                    &nbsp;/&nbsp; De <?= $this->request->query('prazo_de') ?>
                <?php endif; ?>
                <?php if ($this->request->query('prazo_ate')): ?>
                    &nbsp;/&nbsp; Até <?= $this->request->query('prazo_ate') ?>
                <?php endif; ?>
            </h5>

            <!-- Se ele slecionar o filtro setores eu devo mostra quais foram selecionadados
            para garantir a integridade do relatório -->
            <?php if ($setoresSelecionados): ?>
                <h5>
                    <i class="fa fa-folder"></i>&nbsp;&nbsp;<?= $this->Text->toList($setoresSelecionados) ?>
                </h5>
            <?php endif; ?>
            <?php if ($this->request->query('lojas')): ?>
                <h5>
                    <?php
                        $nomesLojas = [];
                        foreach ($visitas as $value) {
                            if (!in_array($value['loja']['nome'], $nomesLojas)) {
                                $nomesLojas[] = $value['loja']['nome'];
                            }
                        }
                    ?>
                    <i class="fa fa-building"></i>&nbsp;&nbsp;<?= $this->Text->toList($nomesLojas) ?>
                </h5>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($dadosDoGrafico): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-title">
                        <ul class="panel-tools">
                            <li>
                                <button
                                    type="button"
                                    class="toggle-parent"
                                    data-plus-icon="fa fa-plus"
                                    data-minus-icon="fa fa-minus"
                                    data-expandido="1"
                                    data-esconder-selector=".panel-body"
                                    data-parent-selector=".panel">
                                        <span class="fa fa-minus"></span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div id="curve_chart" style="width: 100%; height: 200px">
                            Carregando Gráfico...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php // Mesmo tendo filtor pode não ter visitas para mostrar entao eu teste aqui ?>

    <?php if ($visitas): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">

                    <div class="table-content-fixed-with-container text-center">
                        <table class="table-content-fixed-with table table-stripped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th data-width="450" style="">
                                        <button
                                            type="button"

                                            class="meu-btn-icon btn-toggle-todos-setores pull-left" data-para-expandir="1">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </th>
                                    <!-- LOOPS LOJAS -->
                                    <?php foreach ($visitas as $visita): ?>
                                        <th class="text-center" data-width="250">
                                            <?= $this->Html->link($visita['loja']['nome'], ['controller' => 'Visitas', 'action' => 'resultado', 'visitaId' => (int)$visita['id']], ['target' => '_blank']) ?>
                                            <br>
                                            <span style="text-transform: none; font-weight: normal;" class="text-muted"><?= $visita['usuario']['short_name'] ?></span>
                                            <br>
                                            <small style="text-transform: lowercase; font-weight: normal;" class="text-muted"><?= $visita['dt_encerramento']->format('d/m/y \à\s H:i') ?></small>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-center" data-width="200">Média</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $iteracaoPerguntas = 1; ?>
                                <?php foreach ($visitas[0]['checklist']['setores'] as $keySetor => $setor): ?>
                                    <tr style="" class="color4-bg" id="linha-setor-<?= (int)$setor['id'] ?>" data-is-expandido="0">

                                        <td style="" class="text-right">

                                            <button
                                                type="button"
                                                class="pull-left relatorio-toggle-setor meu-btn-icon"
                                                style="margin-right: 10px;"
                                                data-setor-id="<?= (int)$setor['id'] ?>">
                                            </button>

                                            <span style="font-weight: bold; font-size: 16px;">
                                                <?= h($setor['nome']) ?>
                                            </span>
                                        </td>
                                        <td colspan="<?= count($visitas) + 1?>"></td>
                                    </tr>
                                    <?php foreach ($setor['perguntas'] as $keyPergunta => $pergunta): ?>

                                        <!-- Linha da sperguntas -->
                                        <tr id="linha-setor-<?= (int)$setor['id'] ?>" data-is-expandido="0">
                                            <td class="text-right" style="">
                                                <?= $iteracaoPerguntas ?>) <?= $pergunta['pergunta'] ?>
                                            </td>

                                            <!-- Iteração respostas da pergunta -->
                                            <?php
                                                $somaPerguntas = 0;
                                                // Itero somente qundo tem resposta para dividir no final certinho
                                                $totalVisitasComResposta = 0;
                                            ?>
                                            <?php foreach ($visitas as $visita): ?>
                                                <td class="text-center">
                                                    <?php if (isset($visita['checklist']['setores'][$keySetor]['perguntas'][$keyPergunta]['resposta'])): ?>
                                                        <?php
                                                            $resposta = $visita['checklist']['setores'][$keySetor]['perguntas'][$keyPergunta]['resposta'];
                                                        ?>
                                                        <?php if ($pergunta['tipo'] == 1): ?>
                                                            <?php // Contemplando não ter resposta pois a loja não tem o setor ?>
                                                            <?php if ($resposta): ?>
                                                                <?php
                                                                    $totalVisitasComResposta++;
                                                                ?>
                                                                <strong>
                                                                    <?= (!is_null($resposta['alternativa_selecionada']['valor'])) ? number_format($resposta['alternativa_selecionada']['valor'], 0) : '-' ?>
                                                                </strong>
                                                                <br>
                                                                <?= h($resposta['alternativa_selecionada']['alternativa']) ?>
                                                                <?php if ($resposta['observacao']): ?>
                                                                    <br>
                                                                    <a href="#modal-texto" class="open-modal-texto" data-toggle="modal" data-texto="<?= h(nl2br($resposta['observacao'])) ?>" data-title="<?= h($pergunta['pergunta']) ?>"><span class="fa fa-file-text-o"></span> Observação</a>
                                                                <?php endif ?>

                                                                <?php $somaPerguntas += $resposta['alternativa_selecionada']['valor'] ?>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if ($resposta && $resposta['resposta_em_texto']): ?>
                                                                <em>
                                                                    <?= $resposta['resposta_em_texto'] ?>
                                                                </em>
                                                            <?php else: ?>
                                                                <em>-</em>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <!-- MÉDIA DA PERGUNTA -->
                                            <td class="text-center">
                                                <strong>
                                                    <?php
                                                        if ($totalVisitasComResposta) {
                                                            echo number_format(($somaPerguntas / $totalVisitasComResposta), 0);
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?>
                                                </strong>
                                            </td>

                                        </tr>
                                        <?php $iteracaoPerguntas++; ?>
                                    <?php endforeach; ?>

                                    <!-- Linha Resultado setor no final da iteração do setor claro rs -->
                                    <tr class="color4-bg">
                                        <td class="text-right">
                                            <button
                                                type="button"
                                                class="pull-left relatorio-toggle-setor meu-btn-icon"
                                                data-setor-id="<?= (int)$setor['id'] ?>">
                                                <span class="fa fa-minus"></span>
                                            </button>
                                            <strong>Total <span style="text-transform: uppercase"><?= h($setor['nome']) ?></span></strong>
                                        </td>
                                        <?php
                                            $somaSetor = 0;
                                            $somaSetorMaximoPossivel = 0;
                                            // Na media só divido pelo oq realmente teve resposta
                                            $totalVisitasComResposta = 0;
                                        ?>
                                        <?php foreach ($visitas as $visita): ?>
                                            <?php
                                                // Não uso nesse bloco aqui mas é importante pra calcular
                                                // a media no final da tabela
                                                $somaSetor += $visita['checklist']['setores'][$keySetor]['atingido'];
                                                $somaSetorMaximoPossivel += ($visita['checklist']['setores'][$keySetor]['maximo_possivel']) ? $visita['checklist']['setores'][$keySetor]['maximo_possivel'] : 0;
                                                $atingimentoSetor = $visita['checklist']['setores'][$keySetor]['atingido'];

                                                if ($visita['checklist']['setores'][$keySetor]['maximo_possivel']) {
                                                    $atingimentoSetorPorcentagem = round((100*$atingimentoSetor) / $visita['checklist']['setores'][$keySetor]['maximo_possivel']);
                                                } else {
                                                    $atingimentoSetorPorcentagem = 0;
                                                }

                                                $diferencaSetor = $atingimentoSetorPorcentagem - $visitas[0]['checklist']['minimo_esperado'];
                                            ?>

                                            <?php if ($visita['checklist']['setores'][$keySetor]['respondido']): ?>

                                                <?php
                                                    $totalVisitasComResposta++;
                                                ?>

                                                <td class="text-center <?= ($diferencaSetor >= 0) ? 'color7' : 'color10'?>">
                                                    <strong><?= number_format($atingimentoSetor, 0) ?></strong>
                                                    &nbsp;/&nbsp;
                                                    <strong><?= number_format($visita['checklist']['setores'][$keySetor]['maximo_possivel'], 0)  ?></strong>
                                                    <br>
                                                    <strong><?= number_format($atingimentoSetorPorcentagem, 0) ?>%</strong>
                                                    &nbsp;
                                                    (<?= $visitas[0]['checklist']['minimo_esperado'] ?>% <span class="fa fa-caret-<?= ($diferencaSetor >= 0) ? 'up' : 'down' ?>"></span> <?= abs(number_format($diferencaSetor, 0)) ?>)
                                                </td>
                                            <?php else: ?>
                                                <td class="text-center">
                                                    -
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- TD DA MÉDIA DO SETOR -->
                                        <?php
                                            $mediaSetor = ($somaSetor / $totalVisitasComResposta);
                                            $mediaSetorMaximoPossivel = ($somaSetorMaximoPossivel / $totalVisitasComResposta);
                                            if ($setor['maximo_possivel']) {
                                                $atingimentoSetorPorcentagem = round((100*$mediaSetor) / $mediaSetorMaximoPossivel);
                                            } else {
                                                $atingimentoSetorPorcentagem = 0;
                                            }

                                            $diferencaSetor = $atingimentoSetorPorcentagem - $visitas[0]['checklist']['minimo_esperado'];
                                        ?>
                                        <td class="text-center <?= ($diferencaSetor >= 0) ? 'color7' : 'color10'?>">
                                            <strong><?= number_format($mediaSetor, 0) ?></strong>
                                            &nbsp;/&nbsp;
                                            <strong><?= number_format($mediaSetorMaximoPossivel, 0)  ?></strong>
                                            <br>
                                            <strong><?= number_format($atingimentoSetorPorcentagem, 0) ?>%</strong>
                                            &nbsp;
                                            (<?= $visitas[0]['checklist']['minimo_esperado'] ?>% <span class="fa fa-caret-<?= ($diferencaSetor >= 0) ? 'up' : 'down' ?>"></span> <?= abs(number_format($diferencaSetor, 0)) ?>)
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                                <!-- Linha do resumo geral -->

                                <tr>
                                    <td colspan="<?= count($visitas) + 2 ?>"></td>
                                </tr>

                                <tr class="color4-bg">
                                    <td class="text-right">
                                        <strong>Resultado final</strong>
                                    </td>
                                    <?php
                                        $somaAtingimentoGeral = 0;
                                        $somaMaximoPossivelGeral = 0;
                                    ?>
                                    <?php foreach ($visitas as $visita): ?>
                                        <td class="text-center <?= ($visita['atingimento']['diferenca'] >= 0) ? 'color7' : 'color10'?>">
                                            <strong><?= number_format($visita['atingimento']['atingido'], 0) ?></strong>
                                            &nbsp;/&nbsp;
                                            <strong><?= number_format($visita['atingimento']['maximo_possivel'], 0) ?></strong>
                                            <br>
                                            <strong><?= number_format($visita['atingimento']['atingido_porcentagem'], 0) ?>%</strong>
                                            &nbsp;
                                            (<?= $visita['checklist']['minimo_esperado'] ?>% <span class="fa fa-caret-<?= ($visita['atingimento']['diferenca'] >= 0) ? 'up' : 'down' ?>"></span> <?= abs($visita['atingimento']['diferenca']) ?>)
                                        </td>
                                        <?php
                                            $somaAtingimentoGeral += $visita['atingimento']['atingido'];
                                            $somaMaximoPossivelGeral += $visita['atingimento']['maximo_possivel'];
                                        ?>
                                    <?php endforeach; ?>

                                    <!-- MÉDIA GERAL -->
                                    <?php
                                        $mediaGeral = ($somaAtingimentoGeral / count($visitas));
                                        $maximoPossivelGeral = ($somaMaximoPossivelGeral / count($visitas));
                                        
                                        // Se não tem nenhunm conteudo, por exemplo se eu seleciono só perguntas de um setor 
                                        // e setor que nao tem pergunta ele vem nada ai divide por zero e da ruim
                                        $maximoPossivelGeral = ($maximoPossivelGeral) ? $maximoPossivelGeral : 1;
                                        $atingimentoGeralPorcentagem = round((100*$mediaGeral / $maximoPossivelGeral));

                                        $diferencaGeral = $atingimentoGeralPorcentagem - $visitas[0]['checklist']['minimo_esperado'];
                                    ?>
                                    <td class="text-center <?= ($diferencaGeral >= 0) ? 'color7' : 'color10'?>">
                                        <strong><?= number_format($mediaGeral, 0) ?></strong>
                                        &nbsp;/&nbsp;
                                        <strong><?= number_format($maximoPossivelGeral, 0) ?></strong>
                                        <br>
                                        <strong><?= round(number_format($atingimentoGeralPorcentagem, 0)) ?>%</strong>
                                        &nbsp;
                                        (<?= $visitas[0]['checklist']['minimo_esperado'] ?>% <span class="fa fa-caret-<?= ($diferencaGeral >= 0) ? 'up' : 'down' ?>"></span> <?= abs(number_format($diferencaGeral, 0)) ?>)
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


<?= $this->element('Painel/modal_texto') ?>