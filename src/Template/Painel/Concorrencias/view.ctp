<?php
    $title = __('Preços em "{0}"', $concorrencia->descricao);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrências' => ['controller' => 'Concorrencias', 'action' => 'index', 'status' => ($concorrencia->encerrado) ? 'encerradas' : null],
    ]
]) ?>

<div class="panel panel-default">
    <div class="panel-title">
        Legenda
    </div>
    <div class="panel-body" >
        <ul class="list-inline">
            <li>
                <span class="led led-danger led-margin-right"></span> Maior Preço
            </li>
            <li>
                <span class="led led-success led-margin-right"></span> Menor Preço
            </li>
            <li>
                <span class="led led-warning led-margin-right"></span> Preço promocional
            </li>
        </ul>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <ul class="list-unstyled">
            <?php foreach ($concorrentes as $concorrente): ?>
                <li>
                    <?= h($concorrente->nome) ?>
                </li>
                <li>
                    <div class="progress progress-small">
                        <div class="progress-bar progress-bar-success" style="width: 35%"></div>
                        <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 40%"></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-stripped table-hover">
                <thead>
                    <tr>
                        <?php if (!$concorrentes->isEmpty()): ?>
                            <td style="width: 200px"></td>
                            <?php foreach ($concorrentes as $concorrente): ?>
                                <th style="width: 200px">
                                    <?= $concorrente->nome ?>
                                </th>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <th class="text-center">
                                Nenhum concorrente cadastrado.
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($concorrencia->produtos as $produto): ?>
                        <tr>
                            <td>
                                <small>
                                    <?= h($produto->ean) ?>
                                </small>
                                <br>
                                <?= h($produto->descricao) ?>
                            </td>
                            <?php foreach ($concorrentes as $concorrente): ?>
                                <?php if (isset($produto->precos->toArray()[$concorrente->id])): ?>
                                    <td class="text-left" valign="middle">
                                        <?php foreach ($produto->precos->toArray()[$concorrente->id] as $preco): ?>
                                            <div class="">
                                                <?= $this->Number->currency($preco->valor, 'BRL', ['locale' => 'pt_BR']) ?>
                                                <?php if ($preco->valor == $concorrencia->maiores_precos[$produto->id]): ?>
                                                    <span class="led led-danger led-margin-left"></span>
                                                <?php endif; ?>
                                                <?php if ($preco->valor == $concorrencia->menores_precos[$produto->id]): ?>
                                                    <span class="led led-success led-margin-left"></span>
                                                <?php endif; ?>
                                                <?php if ($preco->promocao): ?>
                                                    <span class="led led-warning led-margin-left"></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        -
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$concorrencia->produtos): ?>
                        <tr>
                            <td colspan="<?= $concorrentes->count() ?>" class="text-center">
                                Nenhum produto para mostrar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
