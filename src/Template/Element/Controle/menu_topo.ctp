<div id="top" style="background-color: #2A64AD;">
    <!-- Logo -->
    <div class="applogo">
        <?= $this->Html->image('Controle/logo_branca.png', ['style' => 'margin-top: -14px', 'width' => '130px']) ?>
    </div>
    <!-- Icones de barra do menu -->
    <a href="#" class="sidebar-open-button"><i class="fa fa-bars"></i></a>
    <a href="#" class="sidebar-open-button-mobile"><i class="fa fa-bars"></i></a>
    <!-- UL com os itens da direita -->
    <ul class="top-right dropdown-profile">
        <!-- Menu do usuário -->
        <li class="dropdown link">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle profilebox" >
                <!-- <img src="img/profileimg.png" alt="img"> -->
                <strong><span class="fa fa-user"></span>&nbsp;&nbsp;Daniel</strong><span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-list dropdown-menu-right">
                <li>
                    <?= $this->Html->link('<span class="fa fa-power-off falist"></span> Sair', ['controller' => 'UsuariosControles', 'action' => 'logout'], ['escape' => false]) ?>
                </li>
            </ul>
        </li>
    </ul>
</div>
