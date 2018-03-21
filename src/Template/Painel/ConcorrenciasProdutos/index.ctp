<?php
    $title = __('Lista de Produtos em "{0}"', $concorrencia->descricao);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('../lib/blueimp-file-upload/js/jquery.fileupload', ['block' => true]);
    $this->Html->script('../lib/blueimp-file-upload/js/jquery.iframe-transport', ['block' => true]);

    $this->Html->script('Painel/concorrencias_produtos', ['block' => true]);
?>
<?php $this->start('breadcrumbButtonsRight') ?>
    <div class="dropdown">
        <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">Adicionar produto(s) <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
                <?= $this->Html->link('Manualmente', ['controller' => 'ConcorrenciasProdutos', 'action' => 'add', 'concorrenciaId' => (int)$concorrencia->id]) ?>
                <a href="#" data-toggle="modal" data-target="#my-modal">
                    Do arquivo
                </a>
            </li>
        </ul>
    </div>
<?php $this->end() ?>
<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Concorrências' =>['controller' => 'Concorrencias', 'action' => 'index', 'status' => ($concorrencia->encerrado) ? 'encerradas' : null]
    ]
]) ?>

<!-- Panel Pesquisa -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-title">
                Pesquisar
            </div>
            <div class="panel-body">
                <form class="" action="" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'placeholder' => 'Identificação ou Descrição', 'value' => $this->request->query('q')]) ?>
                        </div>
                        <div class="col-md-8 text-right" style="margin-top: 28px;">
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
                                <?= $this->Html->link($this->Html->icon('pencil'), ['controller' => 'ConcorrenciasProdutos', 'action' => 'edit', 'concorrenciaId' => (int)$concorrencia->id, (int)$produto->id], ['class' => 'btn btn-light btn-xs', 'escape' => false]) ?>
                                <?= $this->Koletor->tabelaBtnDeletar('o Produto', __('{0} - {1}', $produto->ean, $produto->descricao), (int)$produto->id) ?>
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


<div id="my-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Importar produtos</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="csrf_token" id="csrf-token" value="<?= $this->request->_csrfToken ?>">
                        <input
                            type="file"
                            id="fileupload"
                            name="file"
                            class="form-control"
                            data-url="<?= $this->Url->build(['controller' => 'ConcorrenciasProdutos', 'action' => 'uploadFile', '_ext' => 'json']) ?>">
                        <p class="help-block">*TXT ou XML</p>
                    </div>
                    <div class="form-group" id="fileupload-informations">
                    </div>
                    <div class="form-group">
                        <div class="progress progress-striped active progress-small" id="fileupload-progressbar" style="display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-default">Importar produtos</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
