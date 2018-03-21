<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Logs'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Logs Tipos'), ['controller' => 'LogsTipos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Logs Tipo'), ['controller' => 'LogsTipos', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Modulos'), ['controller' => 'Modulos', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Modulo'), ['controller' => 'Modulos', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="logs form large-9 medium-8 columns content">
    <?= $this->Form->create($log) ?>
    <fieldset>
        <legend><?= __('Add Log') ?></legend>
        <?php
            echo $this->Form->control('table_name');
            echo $this->Form->control('ref');
            echo $this->Form->control('logs_tipo_id', ['options' => $logsTipos]);
            echo $this->Form->control('modulo_id', ['options' => $modulos]);
            echo $this->Form->control('criado_em');
            echo $this->Form->control('descricao');
            echo $this->Form->control('autor');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
