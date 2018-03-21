<?php
    echo $this->assign('title', 'Entrar');
    echo $this->assign('panelDesc', '');
?>

<?= $this->Form->create(null, [
    'templates' => [
        'inputContainer' => '{{content}}',
        'label' => '',
    ]
]) ?>
    <div class="group">
        <?= $this->Form->input('email', ['placeholder' => 'Email']) ?>
        <i class="fa fa-at"></i>
    </div>
    <div class="group">
        <?= $this->Form->input('senha', ['placeholder' => 'Senha', 'type' => 'password']) ?>
        <i class="fa fa-key"></i>
    </div>
    <button type="submit" class="btn btn-default btn-block" style="text-transform: uppercase;">
        Entrar
    </button>
<?= $this->Form->end() ?>
