<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= $this->fetch('title') ?> - Maia
        </title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,400i,700,700i" rel="stylesheet">

        <?= $this->AssetCompress->css('painel.default') ?>
        <?= $this->fetch('css') ?>

        <style>
            body{ background: #F5F5F5; }
        </style>
    </head>
<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-form">
                    <div class="form-login-container">
                        <div class="top" style="padding: 100px 0 80px 0">
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

    <?= $this->AssetCompress->script('painel.default') ?>
    <?= $this->Html->script('https://use.fontawesome.com/releases/v5.0.6/js/all.js') ?>
    <?= $this->fetch('script') ?>

</body>
</html>
