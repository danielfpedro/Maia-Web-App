<?php
    $title = 'Itens Críticos';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    echo $this->Html->css('../lib/lightbox2/dist/css/lightbox.min', ['block' => true]);
    echo $this->Html->script('../lib/lightbox2/dist/js/lightbox.min', ['block' => true]);
    echo $this->Html->script('Painel/lightbox_pt-br', ['block' => true]);
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
                                <?php echo $this->Form->input('lojas', ['label' => 'Lojas', 'options' => $lojas, 'data-placeholder' => 'Todas', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('lojas')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('setores', ['label' => 'Setores', 'options' => $setores, 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('setores')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('auditores', ['label' => 'Auditores', 'options' => $usuarios, 'data-placeholder' => 'Todos', 'class' => 'select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'value' => $this->request->query('auditores')]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <?= $this->Form->input('status_resolvido', ['label' => 'Status Resolvido', 'options' => ['Todos', 'Pendentes', 'Resolvidos'], 'value' => $this->request->query('status_resolvido')]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prazo-de">Período</label>
                                <input type="text" id="prazo-de" name="prazo_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('prazo_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="prazo-ate" name="prazo_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('prazo_ate')) ?>">
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
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('visitas.usuario_id', 'Auditor') ?>
                    &nbsp;/&nbsp;
                    <?= $this->Paginator->sort('visitas.lojas.id', 'Loja') ?>
                    &nbsp;/&nbsp;
                    <?= $this->Paginator->sort('perguntas.setor_id', 'Setor') ?>
                    &nbsp;/&nbsp;
                    <?= $this->Paginator->sort('critico_resolvido', 'Data') ?>
                </div>
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('checklists_pergunta_id', 'Pergunta') ?>
                </div>
                <div class="col-sm-4">
                    Observação
                </div>
                <div class="col-sm-2 text-right">
                    Plano de Ação
                </div>
            </div>

            <?php foreach ($itensCriticos as $itemCritico): ?>
                <div class="row table-grid">
                    <div class="col-sm-3">
                        <p>
                            Auditado por <strong><u><?= h($itemCritico->visita->auditor->short_name) ?></u></strong> na loja <strong><u><?= h($itemCritico->visita->loja->nome) ?></u></strong>, setor <strong><u><?= h($itemCritico->pergunta->setor->nome) ?></u></strong> em <strong><u><?= ($itemCritico->dt_resposta) ? $itemCritico->dt_resposta->format('d/m/Y \à\s H:i') : '-' ?></u></strong>.
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <dl class="">
                            <dt>Pergunta</dt>
                            <dd>
                                <?= h($itemCritico->pergunta->pergunta) ?>
                            </dd>
                            <dt>Resposta</dt>
                            <dd>
                                <?= h($itemCritico->alternativas_critica->alternativa) ?>
                            </dd>
                            <dt>Fotos</dt>
                            <dd>
                                <?php
                                    $totalFotos = (is_array($itemCritico->fotos_requeridas)) ? count($itemCritico->fotos_requeridas) : 0;
                                    // Preciso gerar o links para gerar a galeria mas
                                    // quero mostrar só um link ai ele clica e abre a galeria
                                    // entao eu mostro só o primeiro link e o resto eu oculto
                                    $counterLinksFotos = 0;
                                ?>
                                <?php if ($totalFotos > 0): ?>
                                    <?php foreach ($itemCritico->fotos_requeridas as $foto): ?>
                                        <a
                                            href="<?= $this->Url->build($foto->folder . $foto->filename) ?>"
                                            data-lightbox="<?= (int)$itemCritico->pergunta->id ?>"
                                            data-title="<?= ($foto->dt_que_foi_tirada) ? $foto->dt_que_foi_tirada->format('d/m/y \à\s H:i') : '' ?>"
                                            style="<?= ($counterLinksFotos > 0) ? 'display: none;' : '' ?>">

                                            <span class="fa fa-picture-o"></span>&nbsp;<?= $totalFotos ?> Foto<?= ($totalFotos != 1) ? 's' : '' ?>
                                        </a>
                                        <?php $counterLinksFotos++; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-sm-4">
                        <em><?= ($itemCritico->observacao) ? h($itemCritico->observacao) :'-' ?></em>
                    </div>
                    <div class="col-sm-2 text-right">
                        <?php if ($itemCritico->plano_tatico): ?>
                            <p>
                                <?= $this->Html->link('<span class="fa fa-file-text-o"></span> Visualizar Plano', [
                                    'controller' => 'PlanosTaticos',
                                    'action' => 'view',
                                    'respostaId' => $itemCritico->id,
                                    'planoTaticoId' => $itemCritico->plano_tatico->id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-light btn-sm'
                                ]) ?>    
                            </p>
                            
                            <?php if ($itemCritico->plano_tatico->getTotalTarefas()): ?>
                                <p>
                                    <span class="label label-<?= $itemCritico->plano_tatico->getComplementoTarefasClas()?>"><?= $itemCritico->plano_tatico->getTotalTarefasCompletas() ?>/<?= $itemCritico->plano_tatico->getTotalTarefas() ?> Atividades</span>
                                </p>
                            <?php endif ?>

                            <p>
                                De <span class="label label-default"><?= ($itemCritico->plano_tatico->when_end) ? $itemCritico->plano_tatico->when_end->format('d/m/y') : '-' ?></span> até <span class="label label-default"><?= ($itemCritico->plano_tatico->when_end) ? $itemCritico->plano_tatico->when_end->format('d/m/y') : '-' ?></span>
                            </p>
                            
                            <p>
                                <?php $statusInfo = $itemCritico->plano_tatico->getStatusInfo() ?>
                                <span class="label label-<?= $statusInfo['class'] ?>"><?= $statusInfo['title'] . (($statusInfo['extra']) ? ' ('.$statusInfo['extra'].')' : '') ?></span>
                            </p>

                        <?php else: ?>
                            <p>
                                <?= $this->Html->link('<span class="fa fa-plus"></span> Iniciar Plano', [
                                    'controller' => 'PlanosTaticos',
                                    'action' => 'add',
                                    'respostaId' => $itemCritico->id],
                                    [
                                        'class' => 'btn btn-default btn-sm',
                                        'escape' => false
                                    ]) ?>
                            </p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($itensCriticos->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUM ITEM CRÍTICO</strong> para mostrar.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'ChecklistsPerguntasRespostas']) ?>
        </div>

    </div>
</div>
