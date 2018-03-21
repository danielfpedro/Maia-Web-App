<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= $this->fetch('title') ?> - Controle | Octopo
        </title>

        <?= $this->Html->meta('icon');?>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,400i,700,700i" rel="stylesheet">
        <?php
            // Deve ser o Bootstrap que tem no kode, não usar alguma versao mais nova
            // nem que seja da casa do 3.3
            echo $this->Html->css('bootstrap');
            echo $this->Html->css('style');
            echo $this->Html->css('responsive');
            echo $this->Html->css('shortcuts');
            echo $this->Html->css('kode_patch');

            echo $this->Html->css('plugin/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
            echo $this->Html->css('../lib/font-awesome/css/font-awesome.min');
        ?>

        <style>
            body{background: #F5F5F5;}
        </style>
    </head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-form">
                <div class="form-login-container">
                    <div class="top" style="padding: 100px 0 80px 0">
                        <!-- <img src="img/kode-icon.png" alt="icon" class="icon"> -->
                        <?= $this->Html->image('Controle/logo_original.png', ['width' => '300px', 'url' => ['action' => 'login']]) ?>
                        <!-- <h4><?= $this->fetch('panelDesc') ?></h4> -->
                    </div>
                    <div class="form-area">
                        <!-- Renderizo a flash message to tipo Inline -->
                        <?= $this->Flash->render('auth') ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
                <?= $this->fetch('footer') ?>
            </div>
        </div>
    </div>
</div>

    <?= $this->Html->script('../lib/jquery/dist/jquery.min') ?>
    <?= $this->Html->script('../lib/bootstrap/dist/js/bootstrap.min') ?>
    <?= $this->Html->script('plugins') ?>

</body>
</html>
