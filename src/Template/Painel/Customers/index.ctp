<?php
    $title = 'Clientes';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<?php
    $this->start('breadcrumbButtonsRight');
        echo $this->Koletor->btnAdicionar('Adicionar Clientes');
    $this->end()
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => []
]) ?>

<div class="main-panels-container main-panels-large">
    <div class="panel panel-default">
        <div class="panel-title">
            Filtros
        </div>
        <div class="panel-body">
            <form class="" action="" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->input('q', ['label' => 'Nome ou Endereço', 'value' => h($this->request->query('q')), 'placeholder' => 'Nome']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo $this->Form->input('status', ['options' => [true => 'Ativos', false => 'Inativos'], 'empty' => 'Todos', 'value' => h($this->request->query('status'))]) ?>
                        </div>
                    </div>
                    <div class="col-md-3 text-right btn-pesquisar-container">
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

<div class="main-panels-container main-panels-large">
    <div class="panel panel-default">

        <div class="row table-header-grid">
            <div class="col-sm-8">
                <?= $this->Sorter->sort('Customers.name', 'Nome') ?>
            </div>
            <div class="col-md-2 text-right">
                <?= $this->Sorter->sort('Customers.is_active', 'Status') ?>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <?php foreach ($customers as $customer): ?>

            <div class="row table-grid">
                <div class="col-sm-8">
                    <?= h($customer->name) ?>
                </div>
                <div class="col-sm-2 text-right">
                    <?= $this->Koletor->labelBoolean($customer->is_active) ?>
                </div>
                <div class="col-sm-2 text-right">
                    <div class="dropdown">
                        <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class="fa fa-cog"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <?= $this->Html->link('<span class="fa fa fa-pencil-alt fa-fw"></span> Editar', ['action' => 'edit', 'customerId' => (int)$customer->id], ['escape' => false]) ?>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <?= $this->Html->link('<span class="color10"><span class="fa fa-times fa-fw"></span> Remover</span>', '/setor/deletar',
                                    [
                                        'escape' => false,
                                        'class' => 'krazy-btn-delete',
                                        
                                        'krazy-url' => $this->Url->build([
                                            'controller' => 'Customers',
                                            'action' => 'delete',
                                            'customerId' => (int)$customer->id,
                                            '_ext' => 'json',
                                            'method' => 'delete'
                                        ]),
                                        'krazy-message' => 'Você realmente deseja deletar o Cliente <strong>\'' .h($customer->nome). '\'</strong>?',

                                        'data-_csrf-token' => $this->request->_csrfToken,
                                    ])
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($customers->isEmpty()): ?>
            <div class="row table-grid">
                <div class="col-sm-12">
                    <strong>NENHUM CLIENTE</strong> para mostrar.
                </div>
            </div>
        <?php endif; ?>

    <!-- Paginação -->
    <div class="text-right">
        <?= $this->element('Painel/paginator', ['model' => 'Customers']) ?>
    </div>
</div>