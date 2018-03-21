<?php
    $title = 'Planos de Ação';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    echo $this->Html->css('../lib/lightbox2/dist/css/lightbox.min', ['block' => true]);
    echo $this->Html->script('../lib/lightbox2/dist/js/lightbox.min', ['block' => true]);
    echo $this->Html->script('Painel/lightbox_pt-br', ['block' => true]);

    echo $this->Html->script('Painel/interacao_filtros_checklist_setores_perguntas',  ['block' => true]);
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
                                <?php echo $this->Form->input('visita', [
                                    'label' => 'Visita',
                                    'value' => $this->request->query('visita'),
                                    'placeholder' => 'Cod. da Visita',
                                    'class' => 'cod-visita',
                                    'style' => 'text-transform:uppercase'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('checklist', [
                                    'label' => 'Questionário',
                                    'options' => $checklists,
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
                                <?php echo $this->Form->input('setores', ['label' => 'Setores', 'options' => $setores, 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('setores')]) ?>
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
                                <?= $this->Form->input('status', ['label' => 'Status', 'options' => [
                                    'Todos',
                                    'Aguard. elaboração das ativ.',
                                    'Em andamento',
                                    'Finalizado, aguard. aprovação',
                                    'Aprovados',
                                    'Aprovados no prazo',
                                    'Aprovados com Atraso',
                                    'Reprovados',
                                    'Reprovados no Prazo',
                                    'Reprovados com Atraso',
                                ], 'value' => $this->request->query('status')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= $this->Form->input('criado_por', ['label' => 'Criado por', 'options' => $usuarios, 'value' => $this->request->query('criado_por'), 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple']) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= $this->Form->input('responsavel_geral', ['label' => 'Responsável', 'options' => $usuarios, 'value' => $this->request->query('responsavel_geral'), 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple']) ?>
                            </div>
                        </div>
                        <?php if ($this->Koletor->arrayInArray([1, 4], $this->request->session()->read('Auth.Painel.cargos_ids'))): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= $this->Form->input('executante', ['label' => 'Executante', 'options' => $usuarios, 'value' => $this->request->query('executante'), 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple']) ?>
                                </div>
                            </div>                            
                        <?php endif ?>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prazo-de">Dt. Criação</label>
                                <input type="text" id="dt-criacao-de" name="dt_criacao_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('dt_criacao_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="dt-criacao-ate" name="dt_criacao_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('dt_criacao_ate')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prazo-de">Prazo Início</label>
                                <input type="text" id="prazo-inicio-de" name="prazo_inicio_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_inicio_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-inicio-ate" name="prazo_inicio_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_inicio_ate')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prazo-de">Prazo Término</label>
                                <input type="text" id="prazo-termino-de" name="prazo_termino_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_termino_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-termino-ate" name="prazo_termino_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_termino_ate')) ?>">
                            </div>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 25px;">
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
                    Origem do Plano / <?= $this->Paginator->sort('criado_em', 'Dt. Criação') ?>
                </div>
                <div class="col-sm-3">
                </div>
                <div class="col-sm-4">
                    Como?
                </div>
                <div class="col-sm-3 text-right">
                </div>
            </div>

            <?php foreach ($planosTaticos as $planoTatico): ?>
                <div class="row table-grid">
                    <div class="col-sm-2">
                        <dl>
                            <dt>Visita</dt>
                            <dd>
                                <?= $this->Html->link($planoTatico->resposta->visita->cod, ['controller' => 'Visitas', 'action' => 'resultado', 'visitaId' => $planoTatico->resposta->visita->id], ['class' => 'krazy-external-link']) ?>
                            </dd>
                            <dt>Pergunta</dt>
                            <dd>
                                <?= $this->Html->link('Visualizar', [
                                    'controller' => 'ChecklistsPerguntasRespostas',
                                    'action' => 'viewModal',
                                    'respostaId' => $planoTatico->resposta->id
                                    ], [
                                        'toggle' => 'modal',
                                        'data-modal-target' => '#modal-resposta',
                                        'class' => 'abre-modal-ajax'
                                    ]) ?>
                            </dd>
                            <dt>Dt. criação</dt>
                            <span class="label label-default"><?= $planoTatico->criado_em->format('d/m/y') ?></span>
                        </dl>
                    </div>
                    <div class="col-sm-3">
                        <dl>
                            <dt><span class="fa fa-<?= $planoTatico->getTitle('where')['icon'] ?>"></span> Aonde?</dt>
                            <dd>
                                <?= h($planoTatico->resposta->visita->loja->nome) ?> [<?= h($planoTatico->resposta->pergunta->setor->nome) ?>]
                            </dd>
                            <dt><span class="fa fa-<?= $planoTatico->getTitle('what')['icon'] ?>"></span> O que?</dt>
                            <dd><?= nl2br(h($planoTatico->what)) ?></dd>
                            <dt><span class="fa fa-<?= $planoTatico->getTitle('why')['icon'] ?>"></span> Por que?</dt>
                            <dd><?= nl2br(h($planoTatico->why)) ?></dd>
                        </dl>
                    </div>
                    <div class="col-sm-4">
                        <?php if ($planoTatico->tarefas): ?>
                            <?php foreach ($planoTatico->tarefas as $key => $tarefa): ?>
                                <p><strong><?= ($key + 1) ?>) </strong><?= nl2br(h($tarefa->descricao)) ?></p>
                            <?php endforeach ?>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </div>
                    <div class="col-sm-3 text-right">

                        <p>
                            <?= $this->Html->link('Log', [
                                'controller' => 'PlanosTaticos',
                                'action' => 'logsViewModal',
                                'respostaId' => $planoTatico->resposta->id,
                                'planoTaticoId' => $planoTatico->id,
                                ], [
                                    'toggle' => 'modal',
                                    'data-modal-target' => '#modal-log',
                                    'class' => 'btn btn-light abre-modal-ajax'
                                ]) ?>
                            <?= $this->Html->link('<span class="fa fa-bolt"></span> Visualizar Plano', [
                                'controller' => 'PlanosTaticos',
                                'action' => 'view',
                                'respostaId' => $planoTatico->resposta->id,
                                'planoTaticoId' => $planoTatico->id
                            ], [
                                'escape' => false,
                                'class' => 'btn btn-light btn-sm'
                            ]) ?>    
                        </p>
                        
                        <?php if ($planoTatico->getTotalTarefas()): ?>
                            <p>
                                <span class="label label-<?= $planoTatico->getComplementoTarefasClas()?>"><?= $planoTatico->getTotalTarefasCompletas() ?>/<?= $planoTatico->getTotalTarefas() ?> Atividades</span>
                            </p>
                        <?php endif ?>

                        <p>
                            Prazo de <span class="label label-default"><?= ($planoTatico->when_start) ? $planoTatico->when_start->format('d/m/y') : '-' ?></span> até <span class="label label-default"><?= ($planoTatico->when_end) ? $planoTatico->when_end->format('d/m/y') : '-' ?></span>
                        </p>
                        
                        <p>
                            <?= $planoTatico->getStatusLabel() ?>
                        </p>

                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($planosTaticos->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM PLANO DE AÇÃO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'PlanosTaticos']) ?>
        </div>

    </div>
</div>

<div id="modal-log" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Log de Ações do Plano</h4>
            </div>
            <div class="modal-body">
                ...
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-resposta" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Dados da pergunta</h4>
            </div>
            <div class="modal-body">
                ...
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
