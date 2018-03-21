<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Checklist'), ['action' => 'edit', $checklist->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Checklist'), ['action' => 'delete', $checklist->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checklist->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Checklists'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Checklist'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="checklists view large-9 medium-8 columns content">
    <h3><?= h($checklist->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Nome') ?></th>
            <td><?= h($checklist->nome) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ativo') ?></th>
            <td><?= h($checklist->ativo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($checklist->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Grupo Id') ?></th>
            <td><?= $this->Number->format($checklist->grupo_Id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Criado Em') ?></th>
            <td><?= h($checklist->criado_em) ?></td>
        </tr>
    </table>
</div>
