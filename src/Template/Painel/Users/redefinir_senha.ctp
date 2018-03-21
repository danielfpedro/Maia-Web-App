<?php
    echo $this->assign('title', 'Redefinir Senha');
?>
<?= $this->Form->create($usuario, [
    'novalidate' => true,
    'templates' => [
        'inputContainer' => '{{content}}',
        'label' => '',
    ]
]) ?>
    <div class="group">
        <?= $this->Form->input('nova_senha', ['placeholder' => 'Nova Senha', 'type' => 'password']) ?>
        <i class="fa fa-lock"></i>
    </div>
    <div class="group">
        <?= $this->Form->input('confirmar_nova_senha', ['placeholder' => 'Confirmar Nova Senha', 'type' => 'password']) ?>
        <i class="fa fa-lock"></i>
    </div>

    <button type="submit" class="btn btn-default btn-block" style="text-transform: uppercase;">
        Redefinir Senha
    </button>
<?= $this->Form->end() ?>
