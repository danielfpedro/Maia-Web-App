<?php
    $title = ($customer->isNew()) ? 'Adicionar' : __('Editar "{0}"', $customer->name);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('../lib/preenche_cidades/dist/plugin', ['block' => true]);
    $this->Html->script('Painel/Customers/form', ['block' => true]);

    // Breadcrumb
    echo $this->element('Painel/breadcrumb', [
        'items' => [
            'Clientes' => $breadcrumb['index'],
        ]
    ]);
?>

<div class="main-panels-container">
    <?= $this->Form->create($customer, ['novalidate' => true]) ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('name', ['label' => 'Nome']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $this->Form->input('email') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="panel-title">
                    Endereço
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('address.zipcode', ['label' => 'CEP']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('address.state_id', [
                            'label' => 'Cidade',
                            'empty' => 'Selecione:',
                            'data-url' => $this->Url->build([
                                'controller' => 'Cities',
                                'action' => 'byStateId',
                                '_ext' => 'json'
                            ])
                        ]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $this->Form->input('address.city_id', ['label' => 'Estado', 'empty' => 'Selecione o Estado:']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $this->Form->input('address.neighbour', ['label' => 'Bairro']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $this->Form->input('address.description', ['label' => 'Endereço']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="panel-title">
                    Telefones
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <?= $this->Form->input('phones.0.company_id', ['label' => 'Operadora', 'options' => $phonesCompanies, 'empty' => 'Selecione:']) ?>    
                    </div>
                    <div class="col-sm-2">
                        <?= $this->Form->input('phones.0.ddd', ['type' => 'text', 'label' => 'DDD']) ?>
                    </div>
                    <div class="col-sm-5">
                        <?= $this->Form->input('phones.0.number', ['label' => 'Numero']) ?>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-default"><span class="fa fa-plus"></span> Adicionar Telefone</button>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <?= $this->Form->input('is_active', ['label' => 'Ativo', 'checked' => ($customer->isNew() || $customer->is_active), 'hiddenField' => true]) ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body text-right">
                <?= $this->Koletor->btnSalvar() ?>
            </div>
        </div>
    <?= $this->Form->end(); ?>
</div>