<?= $this->assign('title', 'Octopo') ?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand js-scroll-trigger" href="#page-top">
        <?= $this->Html->image('../Site/img/logo_branca.png', ['style' => '', 'class' => 'logo-branco']) ?>
        <?= $this->Html->image('../Site/img/logo_azul.png', ['style' => '', 'class' => 'logo-azul']) ?>
    </a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      Menu
      <i class="fa fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link js-scroll-trigger" href="#download">Baixar App</a>
        </li>
        <li class="nav-item">
          <a class="nav-link js-scroll-trigger" href="#features">Funcionalidades</a>
        </li>
        <li class="nav-item">
          <a class="nav-link js-scroll-trigger" href="#contact">Contato</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100">
      <div class="col-lg-7 my-auto">
        <div class="header-content mx-auto">
          <h1 class="mb-5">
              Dificuldades para <strong>AUDITAR</strong> suas lojas?
          </h1>
          <h3 class="mb-5">
              <strong>OCTOPO</strong> é a ferramenta de Auditoria definitiva para Redes de lojas, Franquias e Consultores.
          </h3>
          <a href="#contact" class="btn btn-outline btn-xl js-scroll-trigger">Entre em contato!</a>
        </div>
      </div>
      <div class="col-lg-5 my-auto">
        <div class="device-container">
          <div class="device-mockup iphone6_plus portrait white">
            <div class="device">
              <div class="screen">
                <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                <?= $this->Html->image('../Site/img/demo-screen-1.jpg', ['class' => 'img-fluid']) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<section class="download bg-primary text-center" id="download">
  <div class="container">
    <div class="row">
      <div class="col-md-8 mx-auto">
        <h2 class="section-heading">Descubra a facilidade em Auditar lojas!</h2>
        <p>Baixe o Aplicativo agora!</p>
        <div class="badges">
            <?= $this->Html->link($this->Html->image('../Site/img/google-play-badge.svg'), 'https://play.google.com/store/apps/details?id=br.com.octopo', ['class' => 'badge-link', 'escape' => false, 'target' => '_blank']) ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="features" id="features">
  <div class="container">
    <div class="section-heading text-center">
      <h2>Funcionalidades que vão salvar muito tempo da sua rotina de Auditoria</h2>
      <p class="text-muted">Veja os principais benefícios do nosso sistema!</p>
      <hr>
    </div>
    <div class="row">

      <div class="col-lg-12 my-auto">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-bell text-primary"></i>
                <h3>Alertas de Itens Críticos</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-share-alt text-primary"></i>
                <h3>Compartilhamento de Resultados</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-directions text-primary"></i>
                <h3>Auditoria de Planograma GC</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-graph text-primary"></i>
                <h3>Análises de desempenho</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-target text-primary"></i>
                <h3>Controle de Metas de conformidade</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
            <div class="col-lg-4">
              <div class="feature-item">
                <i class="icon-location-pin text-primary"></i>
                <h3>Checagem do local da Auditoria</h3>
                <!-- <p class="text-muted">...</p> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="contact bg-primary" id="contact" style="padding: 50px 20px;">
  <div class="container">
    <h2 class="title">Entre em contato!</h2>
    <h2 style="font-weight: bold" class="phone">
        <span class="fa fa-phone"></span> (24) 98128-0685
    </h2>
    <h2 style="font-weight: bold" class="phone">
        <span class="fa fa-envelope-o"></span> contato@octopo.com.br
    </h2>
    <!-- <ul class="list-inline list-social">
      <li class="list-inline-item social-twitter">
        <a href="#">
          <i class="fa fa-twitter"></i>
        </a>
      </li>
      <li class="list-inline-item social-facebook">
        <a href="#">
          <i class="fa fa-facebook"></i>
        </a>
      </li>
      <li class="list-inline-item social-google-plus">
        <a href="#">
          <i class="fa fa-google-plus"></i>
        </a>
      </li>
    </ul> -->
  </div>
</section>


<footer style="padding: 50px 20px">
<div class="container">
<p>&copy; <?= $now->format('Y') ?> OCTOPO. Todos os Direitos Reservados.</p>
</div>
</footer>
