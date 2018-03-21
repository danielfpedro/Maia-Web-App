<?php
    $title = __('{0} atividade', ($planosTaticosTarefa->isNew()) ? 'Adicionar' : 'Editar');
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);


    if ($resposta->plano_tatico->when_end) {
        $this->Html->scriptStart(['block' => true]);
            echo "
                $(function() {
                    $('.date').datepicker('option', 'minDate', 0);
                    $('.date').datepicker('option', 'maxDate', '".$resposta->plano_tatico->when_end->format('d/m/Y')."');
                });
            ";
        $this->Html->scriptEnd();
    }
?>
<!-- Breadcrumb -->
<?php

    $items = [];

    $items['Planos de Ação'] = $breadcrumb['index'];

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
        <?= $this->Form->create($planosTaticosTarefa, ['novalidate' => true]) ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->Form->input('descricao', ['label' => 'Descricao']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('responsavel', ['label' => 'Responsável']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                        <?= $this->Form->input('how_much', ['label' => 'Quanto?', 'type' => 'textarea', 'rows'=> '3']) ?>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-md-4">
                    		<?= $this->Form->input('prazo_placeholder', ['label' => 'Prazo', 'class' => 'date']) ?>
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
