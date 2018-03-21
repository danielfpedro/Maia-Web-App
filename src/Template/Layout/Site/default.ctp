<!DOCTYPE html>
<html lang="pt-br">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>
        <?= $this->fetch('title') ?>
    </title>

    <?= $this->Html->meta('icon') ?>

    <!-- Bootstrap core CSS -->
    <?= $this->Html->css('../Site/lib/bootstrap/css/bootstrap.min.css') ?>

    <!-- Custom fonts for this template -->
    <?= $this->Html->css('../Site/lib/font-awesome/css/font-awesome.min.css') ?>
    <?= $this->Html->css('../Site/lib/simple-line-icons/css/simple-line-icons.css') ?>

    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

    <!-- Plugin CSS -->
    <?= $this->Html->css('../Site/device-mockups/device-mockups.min.css') ?>

    <!-- Custom styles for this template -->
    <?= $this->Html->css('../Site/css/new-age.css') ?>

  </head>

  <body id="page-top">

    <?= $this->fetch('content') ?>

    <!-- Bootstrap core JavaScript -->
    <?= $this->Html->script('../Site/lib/jquery/jquery.min.js') ?>
    <?= $this->Html->script('../Site/lib/popper/popper.min.js') ?>
    <?= $this->Html->script('../Site/lib/bootstrap/js/bootstrap.min.js') ?>

    <!-- Plugin JavaScript -->
    <?= $this->Html->script('../Site/lib/jquery-easing/jquery.easing.min.js') ?>

    <!-- Custom scripts for this template -->
    <?= $this->Html->script('../Site/js/new-age.min.js') ?>

  </body>

</html>
