
<?php
    $this->Html->script('../lib/jquery.maskedinput/dist/jquery.maskedinput.min', ['block' => true]);
    $this->Html->script('Painel/concorrentes', ['block' => true]);

    $title = (!$concorrente->toArray()) ? 'Novo Concorrente' : sprintf('Editar Concorrente "%s"', $concorrente->nome) ;
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrentes' => ['action' => 'index'],
    ]
]) ?>

<div class="row">
    <div class="col-md-12">
        <?= $this->Form->create($concorrente, [
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
                    Dados do concorrente
                </div>
                <div class="panel-body">
                    <?php
                        echo $this->Form->input('nome', ['label' => 'Concorrente']);
                        echo $this->Form->input('loja_id', ['label' => 'Sua loja', 'empty' => 'Selecione a loja:']);
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
