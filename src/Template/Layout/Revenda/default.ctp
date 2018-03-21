<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= $this->fetch('title') ?> - Controle | Octopo
        </title>

        <?= $this->Html->meta('icon') ?>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,400i,700,700i" rel="stylesheet">
        <?php
            // Deve ser o Bootstrap que tem no kode, não usar alguma versao mais nova
            // nem que seja da casa do 3.3
            echo $this->Html->css('bootstrap');
            echo $this->Html->css('style');
            echo $this->Html->css('shortcuts');
            echo $this->Html->css('auditoria');
            echo $this->Html->css('kode_patch');

            echo $this->Html->css('plugin/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
            echo $this->Html->css('../lib/jquery-ui/themes/base/jquery-ui.min');
            echo $this->Html->css('../lib/sweetalert2/dist/sweetalert2.min');
            echo $this->Html->css('../lib/font-awesome/css/font-awesome.min');

            echo $this->Html->css('../lib/select2/dist/css/select2.min');

            echo $this->fetch('css');
        ?>
    </head>

<body data-webroot="<?= $this->request->webroot ?>" data-csrf-token="<?= $this->request->param('_csrfToken') ?>">
    <!-- Start Page Loading -->
    <div class="loading">
        <?= $this->Html->image('loading.gif') ?>
    </div>

    <!-- Menu Topo -->
    <?= $this->element('Controle/menu_topo') ?>
    <!-- Menu Lateral -->
    <?= $this->cell('ControleMenuLateral') ?>

    <!-- Conteúdo -->
    <div class="content">
        <!-- <div class="" style="padding-bottom: 20px;">
            <h5 class="visible-xs">XS</h5>
            <h5 class="visible-sm">SM</h5>
            <h5 class="visible-md">MD</h5>
            <h5 class="visible-lg">LG</h5>
        </div> -->
        <!-- Renderiza o Flash Message -->
        <?= $this->Flash->render() ?>
        <!-- Renderiza action -->
        <?= $this->fetch('content') ?>
    </div>

    <?= $this->Html->script('../lib/jquery/dist/jquery.min') ?>

    <?= $this->Html->script('../lib/jquery-ui/jquery-ui.min') ?>
    <?= $this->Html->script('../lib/jquery-ui/ui/i18n/datepicker-pt-BR') ?>

    <?= $this->Html->script('../lib/bootstrap/dist/js/bootstrap.min') ?>

    <?= $this->Html->script('../lib/sweetalert2/dist/sweetalert2.min') ?>

    <?= $this->Html->script('../lib/jquery.maskedinput/dist/jquery.maskedinput.min') ?>
    <?= $this->Html->script('../lib/select2/dist/js/select2.full.min'); ?>

    <?= $this->Html->script('plugins') ?>


    <?= $this->Html->script('Painel/koletor') ?>

    <?= $this->fetch('script') ?>


</body>
</html>
