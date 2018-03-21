<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
            <?= $this->fetch('title') ?> - <?= $grupoCustomizationData->nome ?>
        </title>

        <meta name="robots" content="noindex">

        <?= $this->Html->meta('icon');?>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,400i,700,700i" rel="stylesheet">

        <?= $this->AssetCompress->css('painel.default') ?>
        <?= $this->fetch('css') ?>
        ?>
    </head>

<body data-webroot="<?= $this->request->webroot ?>" data-csrf-token="<?= $this->request->param('_csrfToken') ?>" style="background-color: #EEE">
    <!-- Start Page Loading -->
    <div class="loading">
        <?= $this->Html->image('loading.gif') ?>
    </div>

    <!-- Conteúdo -->
    <div class="container">
        <!-- Renderiza o Flash Message -->
        <?= $this->Flash->render() ?>
        <!-- Renderiza action -->
        <?= $this->fetch('content') ?>
    </div>

    <?= $this->AssetCompress->script('painel.default') ?>
    <?= $this->Html->script('https://use.fontawesome.com/releases/v5.0.6/js/all.js') ?>
    <?= $this->fetch('script') ?>


</body>
</html>
