<?php
    $title = __('{0} Plano de Ação', ($resposta->plano_tatico->isNew()) ? 'Adicionar' : 'Editar');
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?php

    $items = [];

    if (!$resposta->plano_tatico->isNew()) {
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
    } else {
        $items['Respostas'] = $breadcrumb['index'];
    }

    echo $this->element('Painel/breadcrumb', ['items' => $items])
?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($resposta, ['novalidate' => true]) ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                                $solicitante = $resposta->plano_tatico->getTitle('solicitante');
                                echo $this->Form->input('plano_tatico.solicitante_id', ['label' => $solicitante['title'], 'options' => $solicitantes, 'empty' => 'Selecione:', 'help' => $solicitante['subtitle']]);
                            ?>   
                        </div>
                    </div>
                    <?php
                        $what = $resposta->plano_tatico->getTitle('what');
                        echo $this->Form->input('plano_tatico.what', ['label' => $what['title'], 'help' => $what['subtitle']]);
                        $why = $resposta->plano_tatico->getTitle('why');
                        echo $this->Form->input('plano_tatico.why', ['label' => $why['title'], 'help' => $why['subtitle']]);
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                                $who = $resposta->plano_tatico->getTitle('who');
                                echo $this->Form->input('plano_tatico.who_id', ['label' => $who['title'], 'options' => $executantes, 'empty' => 'Selecione:', 'help' => $who['subtitle']]);
                            ?>   
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUANDO? -->
            <div class="panel panel-default">
                <div class="panel-title">
                    Quando?
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('plano_tatico.when_start_placeholder', ['label' => 'Início', 'class' => 'Início', 'type' => 'text', 'class' => 'date']) ?>  
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('plano_tatico.when_end_placeholder', ['label' => 'Término', 'class' => 'Término', 'type' => 'text', 'class' => 'date']) ?>  
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
