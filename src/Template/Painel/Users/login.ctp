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
    <div class="form-group">
        <?= $this->Form->input('email', ['placeholder' => 'Email']) ?>    
    </div>
    <div class="form-group">
        <?= $this->Form->input('password', ['placeholder' => 'Senha', 'type' => 'password']) ?>
    </div>

    <button type="submit" class="btn btn-default btn-block" style="text-transform: uppercase;">
        Entrar
    </button>
<?= $this->Form->end() ?>

<?= $this->start('footer') ?>
    <div class="footer-links row">
        <div class="col-xs-6">
            <?= $this->Html->link($this->Html->image('getiongoogleplay.png', ['width' => 160]), env('GOOGLEPLAY_APP_URL'), ['escape' => false, 'target' => '_blank', 'style' => 'margin-left: -12px;', 'alt' => 'Baixar aplicativo na Google Play']) ?>
        </div>
    </div>
<?= $this->end() ?>
