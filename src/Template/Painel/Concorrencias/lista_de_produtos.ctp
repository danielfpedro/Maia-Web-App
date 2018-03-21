<?php
    $title = __('Lista de Produtos concorrência "{0}"', $concorrencia->descricao);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>
<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Html->link('Adicionar Produtos', ['controller' => 'ConcorrenciasProdutos', 'action' => 'add', (int)$concorrencia->id], ['class' => 'btn btn-danger']);
    $this->end()
?>
<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrências' => ['action' => 'index']
    ]
]) ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <table class="table table-stripped table-hover">
                <thead>
                    <tr>
                        <th style="width: 150px;">
                            EAN
                        </th>
                        <th>
                            Descrição
                        </th>
                        <th style="width: 150px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($concorrencia->produtos as $produto): ?>

                        <tr>
                            <td><?= h($produto->ean) ?></td>
                            <td>
                                <?= h($produto->descricao) ?>
                            </td>
                            <td class="text-right">
                                <?= $this->Html->link($this->Html->icon('pencil'), ['controller' => 'ConcorrenciasProdutos', 'action' => 'edit', (int)$concorrencia->id, (int)$produto->id], ['class' => 'btn btn-light btn-xs', 'escape' => false]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$concorrencia->produtos): ?>
                        <tr>
                            <td colspan="3">
                                Nenhum produto para mostrar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
