<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= $this->fetch('title') ?> | Octopo
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

            echo $this->fetch('css');
        ?>
    </head>

<body style="background-color: #F5F5F5">

    <div class="error-pages">
        <?= $this->fetch('content') ?>
    </div>

    <?= $this->Html->script('../lib/jquery/dist/jquery.min') ?>

    <?= $this->Html->script('../lib/jquery-ui/jquery-ui.min') ?>

    <?= $this->Html->script('../lib/bootstrap/dist/js/bootstrap.min') ?>

    <?= $this->Html->script('plugins') ?>


    <?= $this->fetch('script') ?>


</body>
</html>
