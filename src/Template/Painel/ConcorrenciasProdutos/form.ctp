<?php
    $title = ($concorrenciasProduto->isNew()) ? __('Adicionar Produto em "{0}"', $concorrencia->descricao) : __('Editar Produto em "{0}"', $concorrencia->descricao) ;
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrências' => ['controller' => 'Concorrencias', 'action' => 'index', 'status' => ($concorrencia->encerrado) ? 'encerradas' : null],
        'Lista de Produtos em ' . $concorrencia->descricao => ['controller' => 'ConcorrenciasProdutos', 'action' => 'index', 'concorrenciaId' => (int)$concorrencia->id],
    ]
]) ?>

<div class="row">
    <div class="col-md-12">
        <?= $this->Form->create($concorrenciasProduto, [
            'horizontal' => true,
            'novalidate' => true,
            'columns' => [
                'md' => [
                    'label' => 2,
                    'input' => 4,
                    'error' => 6
                ]
            ]
        ]); ?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados do Produto
                </div>
                <div class="panel-body">
                    <?php
                        echo $this->Form->input('ean');
                        echo $this->Form->input('descricao');
                    ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body text-right">
                    <?= $this->Koletor->btnSalvar() ?>
                </div>
            </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
