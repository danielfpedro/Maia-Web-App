<?php
    $title = __('Plano de Ação para resposta "[{0}] {1}: {2}"',
            h($this->Text->truncate($resposta->pergunta->setor->nome, 20, ['exact' => 20])),
            h($this->Text->truncate($resposta->pergunta->pergunta, 20, ['exact' => 20])),
            h($this->Text->truncate($resposta->alternativa_selecionada->alternativa, 20, ['exact' => 20])
        ));
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('Painel/PlanosTaticos/index', ['block' => true]);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Planos de Ação' => $breadcrumb['index']
    ]
]) ?>

<div class="row">
    <div class="col-md-5">

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <?php if (!$planosTaticosTarefas->isEmpty()): ?>
                        
                        <!-- Somente ve o botao de trocar status se for adm -->
                        <?php if ($this->Koletor->arrayInArray([1, 4], $this->request->session()->read('Auth.Painel.cargos_ids')) && in_array($resposta->plano_tatico->getStatus(), [2, 4, 5])): ?>
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="fa fa-cog"></span>
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu" role="menu">
                                    
                                    <?php if (!$resposta->plano_tatico->dt_aprovado): ?>
                                        <li>
                                            <?= $this->Html->link('<span class="fa fa-thumbs-up fa-fw"></span> Aprovar', [
                                                'controller' => 'PlanosTaticos',
                                                'action' => 'toggleStatus',
                                                'newStatus' => 1,
                                                'planoTaticoId' => (int)$resposta->plano_tatico->id,
                                                'respostaId' => (int)$resposta->id,
                                                '?' => ['_csrf_token' => $this->request->_csrfToken]
                                            ], [
                                                'escape' => false
                                            ]) ?>
                                        </li>
                                    <?php endif ?>
                                    <?php if (!$resposta->plano_tatico->dt_reprovado): ?>
                                        <li>
                                            <?= $this->Html->link('<span class="fa fa-thumbs-down fa-fw"></span> Reprovar', [
                                                'controller' => 'PlanosTaticos',
                                                'action' => 'toggleStatus',
                                                'newStatus' => 4,
                                                'planoTaticoId' => (int)$resposta->plano_tatico->id,
                                                'respostaId' => (int)$resposta->id,
                                                '?' => ['_csrf_token' => $this->request->_csrfToken]
                                            ], ['escape' => false]) ?>
                                        </li>
                                    <?php endif ?>
                                    <?php if ($resposta->plano_tatico->dt_aprovado || $resposta->plano_tatico->dt_reprovado || $resposta->plano_tatico->dt_cancelamento): ?>
                                        <li>
                                            <?= $this->Html->link('<span class="fa fa-reply fa-fw"></span> Reabrir', [
                                                'controller' => 'PlanosTaticos',
                                                'action' => 'toggleStatus',
                                                'newStatus' => 3,
                                                'planoTaticoId' => (int)$resposta->plano_tatico->id,
                                                'respostaId' => (int)$resposta->id,
                                                '?' => ['_csrf_token' => $this->request->_csrfToken]
                                            ], ['escape' => false]) ?>
                                        </li>                                    
                                    <?php endif ?>
                                </ul>
                            </div>
                        <?php endif ?>
                    <?php endif ?>
                    <div class="panel-title">
                        Status
                    </div>
                    <div class="panel-body">
                        <?= $resposta->plano_tatico->getStatusLabel() ?>

                        <?php if ($resposta->plano_tatico->emAndamento()): ?>
                            <div style="margin-top: 15px;">
                                <?php if (!$resposta->plano_tatico->isVencido()): ?>
                                    <span class="label label-default">
                                        Vence <?= $resposta->plano_tatico->when_end->timeAgoInWords(['accuracy' => 'day']) ?>
                                    </span>
                                    
                                <?php else: ?>
                                    <span class="label label-default">
                                        Vencido <?= $resposta->plano_tatico->when_end->timeAgoInWords() ?>
                                    </span>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                    </div>

                </div>

                <div class="panel panel-default">
                    <?php if ($this->Koletor->arrayInArray([1, 4], $this->request->session()->read('Auth.Painel.cargos_ids'))): ?>
                        <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw-alt fa-sm"></span> Editar', [
                            'controller' => 'PlanosTaticos',
                            'action' => 'edit',
                            'planoTaticoId' => (int)$resposta->plano_tatico->id,
                            'respostaId' => (int)$this->request->respostaId
                            ], [
                                'class' => 'btn btn-default btn-sm pull-right',
                                'escape' => false
                            ])
                        ?>
                    <?php endif?>

                    <!-- WHERE -->    
                    <div class="">
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('where')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('where')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('where')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= h($resposta->visita->loja->nome) ?> [<?= h($resposta->pergunta->setor->nome) ?>]
                        </p>
                        <hr>
                    </div>  
                    <!-- QUEM CRICOU -->
                    <?php if ($resposta->plano_tatico->quem_criou): ?>
                        <div>   
                            <h5>
                                <span class="fa fa-<?= $resposta->plano_tatico->getTitle('quem_criou')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('quem_criou')['title'] ?>
                                <small><?= $resposta->plano_tatico->getTitle('quem_criou')['subtitle'] ?></small>
                            </h5>
                            <p>
                                <?= h($resposta->plano_tatico->quem_criou->nome) ?>
                            </p>
                            <hr>
                        </div>
                    <?php endif ?>


                    <div>   
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('solicitante')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('solicitante')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('solicitante')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= h($resposta->plano_tatico->solicitante->nome) ?>
                        </p>
                        <hr>
                    </div>
                    <!-- WHAT -->    
                    <div class="">
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('what')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('what')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('what')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= nl2br(h($resposta->plano_tatico->what)) ?>
                        </p>
                        <hr>
                    </div>  
                    <!-- WHY -->    
                    <div class="">
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('why')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('why')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('why')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= nl2br(h($resposta->plano_tatico->why)) ?>
                        </p>
                        <hr>
                    </div>  
                    <!-- WHO -->    
                    <div class="">
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('who')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('who')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('who')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= nl2br(h($this->Koletor->propertyDefaultValue($resposta->plano_tatico->who, 'nome', '-'))) ?>
                        </p>
                        <hr>
                    </div>  
                    <!-- WHEN -->    
                    <div class="">
                        <h5>
                            <span class="fa fa-<?= $resposta->plano_tatico->getTitle('when')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('when')['title'] ?>
                            <small><?= $resposta->plano_tatico->getTitle('when')['subtitle'] ?></small>
                        </h5>
                        <p>
                            <?= ($resposta->plano_tatico->when_start) ? $resposta->plano_tatico->when_start->format('d/m/y') : '-' ?> até <?= ($resposta->plano_tatico->when_end) ? $resposta->plano_tatico->when_end->format('d/m/y') : '-' ?>
                        </p>
                        <hr>
                    </div>  
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">

        <!-- OBSERVACAO GERAL SÓ ADM pode ver E EDITAR -->
        <?php if ($this->Koletor->arrayInArray([1, 4], $this->request->session()->read('Auth.Painel.cargos_ids'))): ?>
            <div class="panel panel-default">
                <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw-alt fa-sm"></span> Editar', [
                    'controller' => 'PlanosTaticos',
                    'action' => 'observacaoGeralEdit',
                    'planoTaticoId' => (int)$resposta->plano_tatico->id,
                    'respostaId' => (int)$this->request->respostaId
                    ], [
                        'class' => 'btn btn-default btn-sm pull-right',
                        'escape' => false
                    ])
                ?>
                <div class="panel-title">
                    Observação Geral
                </div>
                <div class="panel-body">
                    <p><?= h($this->Koletor->defaultValue($resposta->plano_tatico->observacao_geral, '-')) ?></p>
                </div>
            </div>
        <?php endif ?>

        <div class="panel panel-default">
            <?php if (!$resposta->plano_tatico->isEncerrado()): ?>
                <?= $this->Html->link('<span class="fa fa-plus"></span> Adicionar Atividade', [
                    'controller' => 'PlanosTaticosTarefas',
                    'action' => 'add',
                    'respostaId' => (int)$this->request->respostaId,
                    'planoTaticoId' => (int)$resposta->plano_tatico->id
                    ], [
                        'class' => 'btn btn-default pull-right btn-sm',
                        'escape' => false
                    ]) ?>    
            <?php endif ?>
            
            <h5>
                <span class="fa fa-<?= $resposta->plano_tatico->getTitle('how')['icon'] ?>"></span> <?= $resposta->plano_tatico->getTitle('how')['title'] ?> <small><?= $resposta->plano_tatico->getTitle('how')['subtitle'] ?></small>

            </h5>

            <br>

            <div class="panel-body">

                <?php if ($planosTaticosTarefas): ?>
                    <div class="row table-header-grid">
                        <div class="col-sm-7"></div>
                        <div class="col-sm-3 text-center">
                            
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <?php foreach ($planosTaticosTarefas as $tarefa): ?>
                        <div class="row table-grid">
                            <div class="col-sm-7">
                                <small><strong>Criado por:</strong></small>
                                <p><?= h(($tarefa->quem_criou) ? $tarefa->quem_criou->short_name : '-') ?></p>
                                <small><strong>Descrição:</strong></small>
                                <p><?= nl2br(h($tarefa->descricao)) ?></p>
                                
                                <?php if ($tarefa->responsavel): ?>
                                    <small><strong>Responsável:</strong></small>
                                    <p><?= nl2br(h($tarefa->responsavel)) ?></p>
                                <?php endif ?>

                                <?php if ($tarefa->how_much): ?>
                                    <small><strong>Quanto?:</strong></small>
                                    <p><?= nl2br(h($tarefa->how_much)) ?></p>
                                <?php endif ?>

                                <?php if (($tarefa->prazo)): ?>
                                    Até o dia <strong><?= $tarefa->prazo->format('d/m/y') ?></strong>    
                                <?php else: ?>
                                    <em>Prazo não informado</em>
                                <?php endif ?>
                                
                            </div>

                            <div class="col-sm-3 text-center">
                                <?= $this->Form->input('concluido.' . $tarefa->id, [
                                    'label' => '<span class="">Concluído?</span>',
                                    'checked' => ($tarefa->dt_concluido),
                                    'type' => 'checkbox',
                                    'disabled' => ($resposta->plano_tatico->isEncerrado()),
                                    'class' => 'check-alterar-completo',
                                    'escape' => false,
                                    'data-url' => $this->Url->build([
                                        '_ext' => 'json',
                                        'controller' => 'PlanosTaticosTarefas',
                                        'action' => 'completoToggle',
                                        'flag' => ':flag',
                                        'tipo' => 1,
                                        'planoTaticoId' => (int)$resposta->plano_tatico->id,
                                        'respostaId' => (int)$this->request->respostaId,
                                        'tarefaId' => (int)$tarefa->id
                                    ]),
                                ]) ?>
                            </div>

                            <div class="col-sm-2 text-right">
                                <?php if (!$resposta->plano_tatico->isEncerrado()): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span class="fa fa-cog"></span>
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw fa-fa-fw"></span> Editar', ['controller' => 'PlanosTaticosTarefas', 'action' => 'edit', 'planoTaticoId' => (int)$resposta->plano_tatico->id, 'respostaId' => (int)$this->request->respostaId, 'tarefaId' => (int)$tarefa->id], ['escape' => false]) ?>
                                            </li>
                                            <li role="separator" class="divider"></li>
                                            <li>
                                                <?= $this->Form->postLink('<span class="color10"><span class="fa fa-times fa-fa-fw"></span> Remover</span>', ['controller' => 'PlanosTaticosTarefas', 'action' => 'delete', 'planoTaticoId' => (int)$resposta->plano_tatico->id, 'respostaId' => (int)$this->request->respostaId, 'tarefaId' => (int)$tarefa->id], ['escape' => false, 'confirm' => __('Você realmente deseja deletar a Tarefa "{0}"?', h($tarefa->descricao))]) ?>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                    <?php if ($planosTaticosTarefas->isEmpty()): ?>
                        <div class="row table-grid">
                            <div class="col-sm-12">
                                <strong>NENHUMA TAREFA</strong> para mostrar.
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif ?>
            </div>

        </div>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'PlanosTaticosTarefas']) ?>
        </div>

    </div>

</div>




<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Resposta</h4>
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