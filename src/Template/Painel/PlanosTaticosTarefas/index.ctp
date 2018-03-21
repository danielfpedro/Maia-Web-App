<?php
    $title = __('Planos Táticos de "{0}"', $resposta->pergunta->pergunta);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
    	echo $this->Html->link('<span class="fa fa-plus"></span> Adicionar Tarefa', ['controller' => 'PlanosTaticosTarefas', 'action' => 'add', 'respostaId' => (int)$this->request->respostaId], ['class' => 'btn btn-default btn-rounded btn-lg', 'escape' => false]);
    $this->end()
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        // 'Planos Táticos' => ['controller' => 'Relatorios', 'action' => 'itensCriticos']
    ]
]) ?>

<!-- <div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-title">
                Pergunta
            </div>
            <div class="panel-body">
                <?= h($resposta->pergunta->pergunta) ?>
                <br>
                
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-title">Resposta</div>
            <div class="panel-body">
                <?= h($resposta->alternativa_selecionada->alternativa) ?>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-title">Obsevação</div>
            <div class="panel-body">
                <?= ($resposta->observacao) ? h($resposta->observacao) : '-' ?>
            </div>
        </div>
    </div>
</div> -->

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-title">Ações</div>
            
            <div class="panel-body">
                <div class="row table-header-grid">
                    <div class="col-sm-2">
                        O que?
                    </div>
                </div>

                <?php foreach ($planosTaticos as $planoTatico): ?>
                    <div class="row table-grid">
                        <div class="col-sm-2">
                            
                        </div>
                    </div>

                <?php endforeach; ?>
                <?php if ($planosTaticosTarefas->isEmpty()): ?>
                    <div class="row table-grid">
                        <div class="col-sm-12">
                            <strong>NENHUM PLANO TÁTICO</strong> para mostrar.
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
