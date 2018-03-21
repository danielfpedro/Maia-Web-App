<?php
    $title = __('Editar Observação');
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?php

    $items = [
        'Planos de Ação' => $breadcrumb['index'],
    ];

    $items[
        __('Plano de Ação para resposta "[{0}] {1}: {2}"',
            h($this->Text->truncate($resposta->pergunta->setor->nome, 20, ['exact' => false])),
            h($this->Text->truncate($resposta->pergunta->pergunta, 20, ['exact' => false])),
            h($this->Text->truncate($resposta->alternativa_selecionada->alternativa, 20, ['exact' => false]))
        )] = [
        'controller' => 'PlanosTaticos',
        'action' => 'view',
        'respostaId' => (int)$this->request->respostaId,
        'planoTaticoId' => (int)$resposta->plano_tatico->id
    ];

    echo $this->element('Painel/breadcrumb', ['items' => $items])

?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($planoTatico, ['novalidate' => true]) ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                                echo $this->Form->input('observacao_geral', ['label' => 'Observação']);
                            ?>   
                        </div>
                    </div>
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
