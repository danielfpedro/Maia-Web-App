<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Grupos'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Segmentos'), ['controller' => 'Segmentos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Segmento'), ['controller' => 'Segmentos', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Checklists'), ['controller' => 'Checklists', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Checklist'), ['controller' => 'Checklists', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Logs'), ['controller' => 'Logs', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Log'), ['controller' => 'Logs', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Lojas'), ['controller' => 'Lojas', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Loja'), ['controller' => 'Lojas', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Modelos Alternativas'), ['controller' => 'ModelosAlternativas', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Modelos Alternativa'), ['controller' => 'ModelosAlternativas', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Setores'), ['controller' => 'Setores', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Setore'), ['controller' => 'Setores', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Visitas'), ['controller' => 'Visitas', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Visita'), ['controller' => 'Visitas', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="grupos form large-9 medium-8 columns content">
    <?= $this->Form->create($grupo) ?>
    <fieldset>
        <legend><?= __('Add Grupo') ?></legend>
        <?php
            echo $this->Form->control('nome');
            echo $this->Form->control('criado_em');
            echo $this->Form->control('ativo');
            echo $this->Form->control('slug');
            echo $this->Form->control('logo_navbar');
            echo $this->Form->control('favicon');
            echo $this->Form->control('cor');
            echo $this->Form->control('logo_navbar_offset_y');
            echo $this->Form->control('logo_navbar_size');
            echo $this->Form->control('logo_email');
            echo $this->Form->control('logo_login');
            echo $this->Form->control('app_font_color');
            echo $this->Form->control('app_bgcolor');
            echo $this->Form->control('app_statusbar_color');
            echo $this->Form->control('app_logo');
            echo $this->Form->control('altura_logo');
            echo $this->Form->control('segmento_id', ['options' => $segmentos]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
