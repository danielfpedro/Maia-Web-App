<?php
    $title = 'Adicionar Visita';
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->css('../js/aehlke-tag-it/css/jquery.tagit', ['block' => true]);
    $this->Html->script('aehlke-tag-it/js/tag-it.min', ['block' => true]);

    $this->Html->script('Painel/visitas', ['block' => true]);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Visitas' => $breadcrumb['index'],
    ]
]) ?>

<!-- Prototipo das caixas de emails -->
<div id="grupos-de-emails-prototipo" class="row" style="display: none;">
    <div class="col-md-12">
        <div class="form-group">
            <label for="">-</label>
            <input type="text" class="form-control">
            <!-- <button type="button" class="btn btn-danger btn-xs pull-right btn-remove-grupo-emails"><span class="fa fa-times"></span> Remover Grupo</button> -->
        </div>
    </div>
</div>

<input type="hidden" id="url-todos-os-grupos-de-emails" value="<?= $this->Url->build(['controller' => 'GruposDeEmails', 'action' => 'autocomplete']) ?>">

<!-- Para preencher as checkboxes -->
<textarea id="usuarios-lojas" rows="8" cols="80" style="display: none;">
<?= json_encode($usuariosComLojas) ?>
</textarea>

<!-- Setores das checklists para mostra alert caso a loja não tenha todos os setores -->
<textarea id="checklists-setores" rows="8" cols="80" style="display: none;">
    <?= json_encode($checklistsComSetores) ?>
</textarea>

<input type="hidden" value="<?= $this->Url->build(['controller' => 'GruposDeEmails', 'action' => 'paraVisitas', '_ext' => 'json']) ?>" id="url-emails">

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">
        <?= $this->Form->create($visitas, ['novalidate' => true]);?>
            <div class="panel panel-default">
                <div class="panel-title">
                    Dados da Visita
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                                // Começa desabilitado pq n ojquery eu preencho as loja e jogo aqui e quando termino eu desabiito la
                                echo $this->Form->input('modelo.usuario_id', ['type' => 'select', 'label' => '*Auditor', 'empty' => 'Selecione o Auditor:', 'disabled' => true, 'options' => $auditores]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('modelo.checklist_id', ['label' => '*Questionário', 'empty' => 'Selecione a Checklist:']);?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="">
                    
                    <div class="container-lojas" style="">
                        <?php foreach ($lojas as $key => $loja): ?>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="container-loja" data-loja-id="<?= (int)$loja->id ?>">
                                        <textarea id="loja-setores" rows="8" cols="80" style="display: none;">
                                            <?= json_encode($loja->setores) ?>
                                        </textarea>

                                        <?= $this->Form->input($loja->id . '.loja_id', [
                                            'class' => 'check-loja',
                                            'type' => 'checkbox',
                                            'label' => $loja->nome,
                                            'value' => (int)$loja->id,
                                            'templates' => [
                                                'checkboxContainer' => '<div class="checkbox checkbox-success {{required}}">{{content}}</div>',
                                            ]]) ?>

                                        <div class="" id="container-loja-extra-options" style="display: none;">
                                            <div class="row">
                                            <div class="col-md-8">
                                                    <?= $this->Form->input($loja->id . '.requerimento_localizacao', [
                                                        'type' => 'select',
                                                        'label' => '*Requerimento de localização',
                                                        'options' => $requerimentoLocalizacaoOptions,
                                                        'templates' => [
                                                            'label' => '<label{{attrs}}><span class="fa fa-map-marker-alt"></span> {{text}}</label>'
                                                        ]
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <?= $this->Form->input($loja->id . '.prazo_placeholder', ['label' => '*Prazo', 'type' => 'text', 'class' => 'date']) ?>
                                                </div>
                                            </div>

                                            <div class="row alert-diferenca-setores" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="kode-alert kode-alert-icon alert5-light"></div>
                                                </div>
                                            </div>

                                            <!-- Emails -->
                                            <hr>
                                            <div class="panel-title">Notificações por email</div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?= $this->Form->input($loja->id . '.grupos_de_emails._ids', ['label' => 'Grupos de Emails', 'data-placeholder' => 'Nenhum', 'class' => 'select2 grupos-de-emails', 'multiple' => 'true', 'style' => 'width: 100%;', 'help' => 'Aqui você insere grupos de Emails previamente cadastrados.'])  ?>
                                                </div>
                                            </div>
                                            <div id="" class="row">
                                                <div class="col-md-6">
                                                    <?= $this->Form->input($loja->id . '.emails_resultados_extras', ['type' => 'text', 'label' => 'Emails RESULTADOS Extras', 'class' => 'has-tagit', 'data-placeholder' => 'Inserir emails...']) ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?= $this->Form->input($loja->id . '.emails_criticos_extras', ['type' => 'text', 'label' => 'Emails CRÍTICOS Extras', 'class' => 'has-tagit', 'data-placeholder' => 'Inserir emails...']) ?>
                                                </div>
                                            </div>

                                            <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php
                                                            $options = [
                                                                'type' => 'checkbox',
                                                                'hiddenField' => true,
                                                                'label' => 'Gerar Planos de Ação automaticamente?',
                                                                'class' => 'toggle-target-from-checkbox',
                                                                'data-target' => 'planos-taticos-pre-info-container',
                                                                'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']];
                                                            echo $this->Form->input($loja->id . '.planos_taticos_pre_info.flag', $options);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="planos-taticos-pre-info-container" style="<?= (!array_key_exists($loja->id, $visitas)  || !$visitas[$loja->id]->planos_taticos_pre_info || !$visitas[$loja->id]->planos_taticos_pre_info->flag) ? 'display: none' : '' ?>">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <?= $this->Form->input($loja->id . '.planos_taticos_pre_info.solicitante_id', ['type' => 'select', 'empty' => 'Selecione:', 'label' => 'Responsável', 'options' => $solicitantes, 'help' => 'Acompanha e valida a execução do plano']) ?>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <?= $this->Form->input($loja->id . '.planos_taticos_pre_info.who_id', ['type' => 'select', 'empty' => 'Selecione:', 'label' => 'Executante', 'options' => $executantes, 'help' => 'Responsável pela elaboração de como fazer e execução deste Plano de Ação']) ?>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <?= $this->Form->input($loja->id . '.planos_taticos_pre_info.prazo_dias', ['type' => 'number', 'label' => 'Prazo (em Dias)', 'help' => 'O <strong>PRAZO</strong> para concluir o plano de ação será baseado na quantidade de dias do campo <strong>PRAZO DIAS</strong> a partir do encerramento da visita.']) ?>
                                                        </div>
                                                    </div>
                                                </div>

                                            <hr>
                                    
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php
                                                        $options = [
                                                            'type' => 'checkbox',
                                                            'hiddenField' => true,
                                                            'templates' => ['checkboxContainerHorizontal' => '<div class="{{inputColumnOffsetClass}} {{inputColumnClass}}"><div class="checkbox {{required}}">{{content}}</div></div>']];
                                                        // Quando tme um erro no submit eu seto esta flag
                                                        // neste caso se não tiver erro(acabou de entrar no form) ele sempre será true, caso contrario
                                                        // será oq vier como valor mesmo
                                                        if (!$visitas['modelo']->errors() && !$lojasError) {
                                                            $options['checked'] = true;
                                                        }
                                                        echo $this->Form->input($loja->id . '.ativo', $options);
                                                    ?>
                                                    <p class="help-block">
                                                        Mantendo esta opção desmarcada a visita será criada mas não ficará disponível para o auditor enquanto ela não for reativada.
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Erro Geral -->
                                            <?php if (isset($visitas[$loja->id]) && $visitas[$loja->id]->errors('geral')): ?>
                                              <div class="row">
                                                  <div class="col-md-12 color10">
                                                      <span class="fa fa-info-circle"></span> A Loja deve conter ao menos um setor da Checklist.
                                                  </div>
                                              </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
