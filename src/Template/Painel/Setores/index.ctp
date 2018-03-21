<?php
    $title = 'Setores';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    // Button Right
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Setor');
    $this->end();

    // Breadcrumb
    echo $this->element('Painel/breadcrumb');
?>

<div class="main-panels-container main-panels-large">
    <!-- Panel Pesquisa -->
    <div class="panel panel-default">
        <div class="panel-title">
            Filtros
        </div>
        <div class="panel-body">
            <form method="GET">
                <div class="row">
                    <!-- Filtro por palavra chave -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $this->Form->input('q', ['label' => 'Palavra Chave', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome']) ?>
                        </div>
                    </div>
                    <!-- Filtro pelo Status -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= $this->Form->input('status', ['options' => [true => 'Ativos', false => 'Inativos'], 'empty' => 'Todos', 'value' => h($this->request->query('status'))]) ?>
                        </div>
                    </div>
                    <!-- Botão que faz o submit do form -->
                    <div class="col-md-3 text-right btn-pesquisar-container">
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">
                                <span class="fa fa-search"></span> Pesquisar
                            </button>
                        </div>
                    </div>
                </div> <!-- Fim da ROW dos inputs do form de pesquisa -->
            </form>
        </div>
    </div>

    <!-- Panel da tabela -->
    <div class="panel panel-default">
        <!-- Header da tabela -->
        <div class="row table-header-grid">
            <div class="col-sm-8">
                <?= $this->Sorter->sort('Setores.nome', 'Nome') ?>
            </div>
            <div class="col-sm-2 text-right">
                <?= $this->Sorter->sort('Setores.ativo', 'Ativo') ?>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <!-- Body da tabela -->
        <?php foreach ($setores as $setor): ?>
            <div class="row table-grid">
                <div class="col-sm-8"><?= h($setor->nome) ?></div>
                <div class="col-sm-2 text-right">
                    <?= $this->Koletor->labelBoolean($setor->ativo) ?>
                </div>
                <div class="col-sm-2 text-right">
                    <div class="dropdown">
                        <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="fa fa-cog"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'setorId' => (int)$setor->id], ['escape' => false]) ?>
                            </li>
                            <li role="separator" class="divider"></li>

                            <li>
                                <?= $this->Html->link('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', '/setor/deletar',
                                    [
                                        'escape' => false,
                                        'class' => 'krazy-btn-delete',
                                        
                                        'krazy-url' => $this->Url->build([
                                            'controller' => 'Setores',
                                            'action' => 'delete',
                                            'setorId' => (int)$setor->id,
                                            '_ext' => 'json',
                                            'method' => 'delete'
                                        ]),
                                        'krazy-message' => 'Você realmente deseja deletar o Setor <strong>\'' .h($setor->nome). '\'</strong>?',

                                        'data-_csrf-token' => $this->request->_csrfToken,
                                    ])
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Caso não tenha dados mostra aqui -->
        <?php if ($setores->isEmpty()): ?>
            <div class="row table-grid">
                <div class="col-sm-12">
                    <strong>NENHUM SETOR</strong> para mostrar.
                </div>
            </div>
        <?php endif; ?>

        <!-- Paginação -->
        <div class="text-right">
            <?= $this->element('Painel/paginator') ?>
        </div>
    </div>
</div>