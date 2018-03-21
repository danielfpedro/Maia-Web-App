<?php
    $title = ($setor->isNew()) ? 'Adicionar' : __('Editar "{0}"', $setor->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    // Breadcrumb
    echo $this->element('Painel/breadcrumb', [
        'items' => [
            'Setores' => $breadcrumb['index'],
        ]
    ]);
?>

<div class="main-panels-container">
    <?= $this->Form->create($setor, ['novalidate' => true]) ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $this->Form->input('nome') ?>
            </div>
        </div>

        <?php if ($this->request->getParam('action') == 'add'): ?>
            <div class="panel panel-default painel-lojas">
                <div class="panel-title">
                    Lojas
                    <span class="panel-subtitle">
                        Quando este setor for criado ele será automaticamente ligado as Lojas selecionadas abaixo.
                    </span>
                </div>
                <div class="panel-body">
                    <?= $this->Form->input('lojas._ids', [
                        'multiple' => 'checkbox',
                        'label' => false,
                        'value' => ($setor->isNew()) ? array_keys($lojas->toArray()) : null
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-body">
                <?= $this->Form->input('ativo', ['cheked' => ($setor->isNew()), 'hiddenField' => true]) ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body text-right">
                <?= $this->Koletor->btnSalvar() ?>
            </div>
        </div>
    <?= $this->Form->end(); ?>
</div>