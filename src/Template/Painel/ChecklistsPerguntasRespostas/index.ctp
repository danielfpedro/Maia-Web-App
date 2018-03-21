<?php
    $title = 'Itens Avaliados';
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
                                <?php echo $this->Form->input('visita', [
                                    'label' => 'Visita',
                                    'value' => $this->request->query('visita'),
                                    'placeholder' => 'Cod. da Visita',
                                    'class' => 'cod-visita',
                                    'style' => 'text-transform:uppercase'
                                ]) ?>
                            </div>
                        </div>
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
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= $this->Form->input('planos_acao', ['label' => 'Planos de Ação', 'options' => [
                                    'Todas',
                                    'Sem plano de ação criado',
                                    'Somente com plano de ação criado',
                                    ],
                                    'value' => $this->request->query('planos_acao')
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= $this->Form->input('tipo_resposta', ['label' => 'Tipo da Resposta', 'options' => [
                                    'Todos',
                                    'Somente críticas',
                                    'Somente não críticas',
                                ], 'value' => $this->request->query('tipo_resposta')]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= $this->Form->input('critico', ['label' => 'Críticos', 'options' => [
                                    'Todos',
                                    'Somente Críticos resolvidos',
                                    'Somente Críticos não resolvidos',
                                ], 'value' => $this->request->query('critico')]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prazo-de">Período da Resposta</label>
                                <input type="text" id="resposta-de" name="resposta_de" class="form-control date" placeholder="De" value="<?= h($this->request->query('resposta_de')) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <input type="text" id="resposta-ate" name="resposta_ate" class="form-control date" placeholder="Até" value="<?= h($this->request->query('resposta_ate')) ?>">
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

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">

            <div class="row table-header-grid">
                <div class="col-sm-3">
                    <?= $this->Paginator->sort('visitas.cod', 'Visita') ?>
                    &nbsp;/&nbsp;
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
                </div>
            </div>

            <?php foreach ($itensCriticos as $itemCritico): ?>
                <div class="row table-grid">
                    <div class="col-sm-3">
                        <dl>
                            <dt>Visita</dt>
                            <dd><?= $this->Html->link($itemCritico->visita->cod, ['controller' => 'Visitas', 'action' => 'resultado', 'visitaId' => (int)$itemCritico->visita->id], ['class' => 'krazy-external-link']) ?></dd>
                            <dt>Informações</dt>
                            <dd>
                                Auditado por <strong><u><?= h($itemCritico->visita->auditor->short_name) ?></u></strong> na loja <strong><u><?= h($itemCritico->visita->loja->nome) ?></u></strong>, setor <strong><u><?= h($itemCritico->pergunta->setor->nome) ?></u></strong> em <strong><u><?= ($itemCritico->dt_resposta) ? $itemCritico->dt_resposta->format('d/m/Y \à\s H:i') : '-' ?></u></strong>.        
                            </dd>
                        </dl>
                        
                    </div>
                    <div class="col-sm-3">
                        <dl class="">
                            <dt>Pergunta</dt>
                            <dd>
                                <?= h($itemCritico->pergunta->pergunta) ?>
                            </dd>
                            <dt>Resposta</dt>
                            <dd>
                                <?php if ($itemCritico->alternativa_selecionada->item_critico): ?>
                                    <span class="label label-danger label-xs"><span class="fa fa-exclamation-triangle"></span> Crítico</span>
                                <?php endif ?>
                                <?= h($itemCritico->alternativa_selecionada->alternativa) ?>
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

                                            <span class="fa fa-images"></span>&nbsp;<?= $totalFotos ?> Foto<?= ($totalFotos != 1) ? 's' : '' ?>
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
                        <em><?= ($itemCritico->observacao) ? nl2br(h($itemCritico->observacao)) :'-' ?></em>
                    </div>
                    <div class="col-sm-2 text-right">

                        <?php if ($itemCritico->alternativa_selecionada->item_critico && !$itemCritico->plano_tatico): ?>
                            <div class="checkbox checkbox-primary">
                                <input
                                    id="critico-resolvido-<?= (int)$itemCritico->id ?>"
                                    type="checkbox"
                                    class="btn-save-resolvido"
                                    data-url="<?= $this->Url->build(['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'toggleCriticoResolvido', 'respostaId' => (int)$itemCritico->id, 'method' => 'POST', '_ext' => 'json']) ?>"
                                    data-resposta-id="<?= (int)$itemCritico->id ?>"
                                    <?= ((boolean)$itemCritico->critico_resolvido) ? 'checked' : '' ?>>
                                <label for="critico-resolvido-<?= (int)$itemCritico->id ?>">Crítico resolvido?</label>
                            </div>

                            <?php if ($itemCritico->critico_resolvido): ?>
                                <span class="label label-default">
                                    <?= $itemCritico->critico_resolvido->format('d/m/Y \à\s H:i') ?>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif ?>

                        
                        <div style="margin-top: 15px;">
                            <?php if ($itemCritico->plano_tatico): ?>
                                <?= $this->Html->link('<span class="fa fa-bolt"></span> Visualizar Plano', [
                                    'controller' => 'PlanosTaticos',
                                    'action' => 'view',
                                    'respostaId' => $itemCritico->id,
                                    'planoTaticoId' => $itemCritico->plano_tatico->id
                                    ],
                                    [
                                        'class' => 'btn btn-light btn-sm',
                                        'escape' => false
                                    ]) ?>
                            <?php else: ?>
                                <?= $this->Html->link('<span class="fa fa-plus"></span> Criar Plano', [
                                    'controller' => 'PlanosTaticos',
                                    'action' => 'add',
                                    'respostaId' => $itemCritico->id],
                                    [
                                        'class' => 'btn btn-default btn-sm',
                                        'escape' => false
                                    ]) ?>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($itensCriticos->isEmpty()): ?>
                <div class="row table-grid">
                    <div class="col-sm-12">
                        <strong>NENHUMA RESPOSTA</strong> para mostrar.
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
