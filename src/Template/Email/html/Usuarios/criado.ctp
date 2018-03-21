<?php
    $this->assign('logo', $usuario->grupo->logo_email_path);
    $this->assign('temBotao', true);
    $this->assign('buttonText', 'Acessar Octopo | ' . $usuario->grupo->nome);
?>

<?= $this->start('textoPrincipal') ?>
    <p>Olá <strong><?= $usuario->short_name ?></strong></p>

    <p>
        Você já pode acessar o sistema usando o seu email "<strong><?= $usuario->email ?></strong>" como login. A senha será passada a você por quem criou o seu usuário.
    </p>
    <p>
      <strong>Login</strong>: <?= $usuario->email ?>
      <br>
      <strong>Senha</strong>: Quem criou deverá lhe <a href="#">clicando aqui</a>.
    </p>
<?= $this->end() ?>
