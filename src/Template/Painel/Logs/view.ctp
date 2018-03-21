<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Log $log
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Log'), ['action' => 'edit', $log->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Log'), ['action' => 'delete', $log->id], ['confirm' => __('Are you sure you want to delete # {0}?', $log->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Logs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Log'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Logs Tipos'), ['controller' => 'LogsTipos', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Logs Tipo'), ['controller' => 'LogsTipos', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Modulos'), ['controller' => 'Modulos', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Modulo'), ['controller' => 'Modulos', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="logs view large-9 medium-8 columns content">
    <h3><?= h($log->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Table Name') ?></th>
            <td><?= h($log->table_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logs Tipo') ?></th>
            <td><?= $log->has('logs_tipo') ? $this->Html->link($log->logs_tipo->id, ['controller' => 'LogsTipos', 'action' => 'view', $log->logs_tipo->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modulo') ?></th>
            <td><?= $log->has('modulo') ? $this->Html->link($log->modulo->id, ['controller' => 'Modulos', 'action' => 'view', $log->modulo->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($log->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ref') ?></th>
            <td><?= $this->Number->format($log->ref) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Autor') ?></th>
            <td><?= $this->Number->format($log->autor) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Criado Em') ?></th>
            <td><?= h($log->criado_em) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Descricao') ?></h4>
        <?= $this->Text->autoParagraph(h($log->descricao)); ?>
    </div>
</div>
