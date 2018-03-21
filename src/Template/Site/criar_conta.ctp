<div class="container-fluid">
    <div class="row" style="margin-top: 50px;">
        <div class="col-md-4 col-md-offset-4">
            <h4>Usuário</h4>
            <hr>
            <?php
                echo $this->Form->create($grupo, ['novalidate' => true]);
                    echo $this->Form->input('usuarios.0.nome');
                    echo $this->Form->input('usuarios.0.email');
                    echo $this->Form->input('usuarios.0.senha', ['type' => 'password']);
            ?>
            <br>
            <h4>Empresa</h4>
            <hr>
            <?php
                    echo $this->Form->input('lojas.0.nome');
                    echo $this->Form->button('Criar conta', ['class' => 'btn btn-danger btn-lg btn-block']);
                echo $this->Form->end();
            ?>
        </div>
    </div>
</div>
