<div id="top" style="">
    <!-- Logo -->
    <div class="applogo">
    </div>
    <!-- Icones de barra do menu -->
    <a href="#" class="sidebar-open-button"><i class="fa fa-bars"></i></a>
    <a href="#" class="sidebar-open-button-mobile"><i class="fa fa-bars"></i></a>
    <!-- UL com os itens da direita -->
    <ul class="top-right">
        <!-- Menu do usuário -->
        <li class="dropdown link">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle profilebox">
                <!-- <img src="img/profileimg.png" alt="img"> -->
                <strong><span class="fa fa-user"></span>&nbsp;&nbsp;<?= $this->request->session()->read('Auth.Painel.name'); ?></strong><span class="caret" style=""></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-list dropdown-menu-right">
            </ul>
        </li>
    </ul>
</div>