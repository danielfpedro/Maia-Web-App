<?php
    echo $this->assign('title', 'Solicitar Redefinição de Senha');
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

    <button type="submit" class="btn btn-default btn-block" style="text-transform: uppercase;">
        Solicitar Redefinição
    </button>
<?= $this->Form->end() ?>


<?= $this->start('footer') ?>
    <div class="footer-links row">
        <div class="col-xs-6">
        </div>
        <div class="col-xs-6 text-right">
        </div>
    </div>
<?= $this->end() ?>
