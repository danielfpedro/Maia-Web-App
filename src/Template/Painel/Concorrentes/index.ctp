<?php
    $title = 'Grupos de Perguntas';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Grupo');
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
                Filtros
            </div>
            <div class="panel-body">
                <form class="" action="" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php echo $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => $this->request->query('q'), 'placeholder' => 'Nome ou Email']) ?>
                            </div>
                        </div>
                        <div class="col-md-5 text-right" style="margin-top: 25px;">
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
                            <th>
                                Nome
                            </th>
                            <th class="text-center" style="width: 250px;">
                                Status
                            </th>
                            <th style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($setores as $setor): ?>

                            <tr>
                                <td><?= h($setor->nome) ?></td>
                                <td class="text-center">
                                    <?= $this->Koletor->labelBoolean($setor->ativo) ?>
                                </td>
                                <td class="text-right">
                                    <?= $this->Html->link($this->Koletor->icon('pencil'), ['action' => 'edit', (int)$setor->id], ['class' => 'btn btn-light btn-icon btn-xs', 'escape' => false]) ?>
                                    <?= $this->Form->postLink($this->Koletor->icon('remove'), ['action' => 'delete', (int)$setor->id], ['class' => 'btn btn-light btn-xs btn-icon', 'escape' => false, 'confirm' => __('Você realmente deseja deletar o usuário "{0}"?', h($setor->nome))]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($setores->isEmpty()): ?>
                            <tr>
                                <td colspan="4">
                                    Nenhum grupo para mostrar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator', ['model' => 'Setores']) ?>
        </div>
    </div>
</div>
