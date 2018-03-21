<?php
    $title = __('Concorrências {0}', str_replace('-', ' ', $this->request->status));
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
    $this->Html->script('Painel/concorrencias', ['block' => true]);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        /**
         * Só tme botão de adicionar se estiver nos em andamento e não no encerrados
         */
        if (!$this->request->status) {
            echo $this->Html->link('Adicionar Concorrência', ['action' => 'add'], ['class' => 'btn btn-danger']);
        }
    $this->end()
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
                Pesquisar
            </div>
            <div class="panel-body">
                <form class="" action="" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'placeholder' => 'Identificação ou Descrição', 'value' => $this->request->query('q')]) ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->Form->input('loja', ['options' => $lojas, 'empty' => 'Todas', 'value' => $this->request->query('loja')]) ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->Form->input('de', ['class' => 'date', 'placeholder' => 'Data inicial', 'value' => $this->request->query('de')]) ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->Form->input('ate', ['label' => 'Até', 'placeholder' => 'Data limite', 'class' => 'date', 'value' => $this->request->query('ate')]) ?>
                        </div>
                        <div class="col-md-2 text-right" style="margin-top: 25px;">
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
            <div class="table-responsive">
                <table class="table table-stripped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 150px;">
                                Identificação
                            </th>
                            <th>
                                Descrição
                            </th>
                            <th>
                                Loja
                            </th>
                            <th class="text-center">
                                Data
                            </th>
                            <th class="text-center">
                                Status
                            </th>
                            <th style="width: 250px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($concorrencias as $concorrencia): ?>

                            <tr>
                                <td><?= $concorrencia->identificacao ?></td>
                                <td>
                                    <?= h($concorrencia->descricao) ?>
                                </td>
                                <td>
                                    <?= h($concorrencia->loja->nome) ?>
                                </td>
                                <td class="text-center">
                                    <?= $concorrencia->criado_em->format('d/M/y') ?>
                                </td>
                                <td class="text-center">
                                    <?= $this->Koletor->labelBoolean(!(boolean)$concorrencia->encerrado, 'Em andamento', 'Encerrado') ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                        $text = 'Encerrar';
                                        $icon = 'stop';

                                        if ($concorrencia->encerrado) {
                                            $text = 'Reativar';
                                            $icon = 'repeat';
                                        }
                                    ?>
                                    <button
                                        type="button"
                                        class="btn btn-light btn-xs btn-post-ajax"
                                        data-url="<?= $this->Url->build(['controller' => 'Concorrencias', 'action' => 'toggleStatus', '_ext' => 'json', (int)$concorrencia->id])?>"
                                        data-url-redirect="<?= $this->Url->build(['controller' => 'Concorrencias', 'status' => $this->request->status])?>"
                                        data-confirm-pergunta="Você realmente deseja <?= ($concorrencia->encerrado) ? 'reativar' : 'encerrar' ?> esta concorrência?"
                                        data-loading-text="<?= ($concorrencia->encerrado) ? 'Reativando' : 'Encerrando' ?> concorrência. Por favor, aguarde..."
                                        data-post-data="{'_csrfToken' : '<?= $this->request->param('_csrfToken'); ?>'}"
                                        data-show-success-message="false"
                                        title="<?= $text ?>">
                                        <span class="glyphicon glyphicon-<?= $icon ?>"></span>
                                    </button>
                                    <?= $this->Html->link($this->Html->icon('pencil'),
                                        [
                                            'action' => 'edit',
                                            (int)$concorrencia->id
                                        ],
                                        [
                                            'class' => 'btn btn-light btn-xs',
                                            'title' => 'Editar',
                                            'escape' => false
                                        ])
                                    ?>
                                    <?= $this->Html->link($this->Html->icon('shopping-cart'), ['controller' => 'ConcorrenciasProdutos', 'action' => 'index', 'concorrenciaId' => (int)$concorrencia->id], ['title' => 'Lista de produtos', 'class' => 'btn btn-light btn-xs', 'escape' => false]) ?>
                                    <?= $this->Html->link('Resultados', ['action' => 'view', 'concorrenciaId' => (int)$concorrencia->id], ['class' => 'btn btn-light btn-xs', 'escape' => false]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($concorrencias->isEmpty()): ?>
                            <tr>
                                <td colspan="3">
                                    Nenhuma concorrência para mostrar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
