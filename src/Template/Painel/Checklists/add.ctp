<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Checklists'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="checklists form large-9 medium-8 columns content">
    <?= $this->Form->create($checklist) ?>
    <fieldset>
        <legend><?= __('Add Checklist') ?></legend>
        <?php
            echo $this->Form->input('nome');
            echo $this->Form->input('grupo_Id');
            echo $this->Form->input('criado_em');
            echo $this->Form->input('ativo');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
