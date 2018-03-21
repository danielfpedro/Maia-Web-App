<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Grupo $grupo
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Grupo'), ['action' => 'edit', $grupo->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Grupo'), ['action' => 'delete', $grupo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $grupo->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Grupos'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Grupo'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Segmentos'), ['controller' => 'Segmentos', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Segmento'), ['controller' => 'Segmentos', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Checklists'), ['controller' => 'Checklists', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Checklist'), ['controller' => 'Checklists', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Logs'), ['controller' => 'Logs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Log'), ['controller' => 'Logs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Lojas'), ['controller' => 'Lojas', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Loja'), ['controller' => 'Lojas', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Modelos Alternativas'), ['controller' => 'ModelosAlternativas', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Modelos Alternativa'), ['controller' => 'ModelosAlternativas', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Setores'), ['controller' => 'Setores', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Setore'), ['controller' => 'Setores', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Usuarios'), ['controller' => 'Usuarios', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Usuario'), ['controller' => 'Usuarios', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Visitas'), ['controller' => 'Visitas', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Visita'), ['controller' => 'Visitas', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="grupos view large-9 medium-8 columns content">
    <h3><?= h($grupo->nome) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Nome') ?></th>
            <td><?= h($grupo->nome) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ativo') ?></th>
            <td><?= h($grupo->ativo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Slug') ?></th>
            <td><?= h($grupo->slug) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logo Navbar') ?></th>
            <td><?= h($grupo->logo_navbar) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Favicon') ?></th>
            <td><?= h($grupo->favicon) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cor') ?></th>
            <td><?= h($grupo->cor) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logo Email') ?></th>
            <td><?= h($grupo->logo_email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logo Login') ?></th>
            <td><?= h($grupo->logo_login) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('App Font Color') ?></th>
            <td><?= h($grupo->app_font_color) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('App Bgcolor') ?></th>
            <td><?= h($grupo->app_bgcolor) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('App Statusbar Color') ?></th>
            <td><?= h($grupo->app_statusbar_color) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('App Logo') ?></th>
            <td><?= h($grupo->app_logo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Segmento') ?></th>
            <td><?= $grupo->has('segmento') ? $this->Html->link($grupo->segmento->nome, ['controller' => 'Segmentos', 'action' => 'view', $grupo->segmento->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($grupo->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logo Navbar Offset Y') ?></th>
            <td><?= $this->Number->format($grupo->logo_navbar_offset_y) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logo Navbar Size') ?></th>
            <td><?= $this->Number->format($grupo->logo_navbar_size) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Altura Logo') ?></th>
            <td><?= $this->Number->format($grupo->altura_logo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Criado Em') ?></th>
            <td><?= h($grupo->criado_em) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Checklists') ?></h4>
        <?php if (!empty($grupo->checklists)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Nome') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Observacao') ?></th>
                <th scope="col"><?= __('Minimo Esperado') ?></th>
                <th scope="col"><?= __('Dt Modificado') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col"><?= __('Deletado') ?></th>
                <th scope="col"><?= __('Sem Agendamento Flag') ?></th>
                <th scope="col"><?= __('Segmento Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->checklists as $checklists): ?>
            <tr>
                <td><?= h($checklists->id) ?></td>
                <td><?= h($checklists->nome) ?></td>
                <td><?= h($checklists->grupo_id) ?></td>
                <td><?= h($checklists->ativo) ?></td>
                <td><?= h($checklists->observacao) ?></td>
                <td><?= h($checklists->minimo_esperado) ?></td>
                <td><?= h($checklists->dt_modificado) ?></td>
                <td><?= h($checklists->criado_em) ?></td>
                <td><?= h($checklists->modificado_em) ?></td>
                <td><?= h($checklists->culpado_novo_id) ?></td>
                <td><?= h($checklists->culpado_modificacao_id) ?></td>
                <td><?= h($checklists->deletado) ?></td>
                <td><?= h($checklists->sem_agendamento_flag) ?></td>
                <td><?= h($checklists->segmento_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Checklists', 'action' => 'view', $checklists->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Checklists', 'action' => 'edit', $checklists->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Checklists', 'action' => 'delete', $checklists->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checklists->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Logs') ?></h4>
        <?php if (!empty($grupo->logs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Table Name') ?></th>
                <th scope="col"><?= __('Ref') ?></th>
                <th scope="col"><?= __('Logs Tipo Id') ?></th>
                <th scope="col"><?= __('Modulo Id') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Descricao') ?></th>
                <th scope="col"><?= __('Autor Id') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->logs as $logs): ?>
            <tr>
                <td><?= h($logs->id) ?></td>
                <td><?= h($logs->table_name) ?></td>
                <td><?= h($logs->ref) ?></td>
                <td><?= h($logs->logs_tipo_id) ?></td>
                <td><?= h($logs->modulo_id) ?></td>
                <td><?= h($logs->criado_em) ?></td>
                <td><?= h($logs->descricao) ?></td>
                <td><?= h($logs->autor_id) ?></td>
                <td><?= h($logs->grupo_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Logs', 'action' => 'view', $logs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Logs', 'action' => 'edit', $logs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Logs', 'action' => 'delete', $logs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $logs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Lojas') ?></h4>
        <?php if (!empty($grupo->lojas)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Nome') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Cnpj') ?></th>
                <th scope="col"><?= __('Cep') ?></th>
                <th scope="col"><?= __('Cidade Id') ?></th>
                <th scope="col"><?= __('Endereco') ?></th>
                <th scope="col"><?= __('Bairro') ?></th>
                <th scope="col"><?= __('Lat') ?></th>
                <th scope="col"><?= __('Lng') ?></th>
                <th scope="col"><?= __('Emails Criticos') ?></th>
                <th scope="col"><?= __('Emails Receber Resultado') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col"><?= __('Deletado') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->lojas as $lojas): ?>
            <tr>
                <td><?= h($lojas->id) ?></td>
                <td><?= h($lojas->nome) ?></td>
                <td><?= h($lojas->criado_em) ?></td>
                <td><?= h($lojas->modificado_em) ?></td>
                <td><?= h($lojas->grupo_id) ?></td>
                <td><?= h($lojas->ativo) ?></td>
                <td><?= h($lojas->cnpj) ?></td>
                <td><?= h($lojas->cep) ?></td>
                <td><?= h($lojas->cidade_id) ?></td>
                <td><?= h($lojas->endereco) ?></td>
                <td><?= h($lojas->bairro) ?></td>
                <td><?= h($lojas->lat) ?></td>
                <td><?= h($lojas->lng) ?></td>
                <td><?= h($lojas->emails_criticos) ?></td>
                <td><?= h($lojas->emails_receber_resultado) ?></td>
                <td><?= h($lojas->culpado_novo_id) ?></td>
                <td><?= h($lojas->culpado_modificacao_id) ?></td>
                <td><?= h($lojas->deletado) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Lojas', 'action' => 'view', $lojas->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Lojas', 'action' => 'edit', $lojas->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Lojas', 'action' => 'delete', $lojas->id], ['confirm' => __('Are you sure you want to delete # {0}?', $lojas->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Modelos Alternativas') ?></h4>
        <?php if (!empty($grupo->modelos_alternativas)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Nome') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->modelos_alternativas as $modelosAlternativas): ?>
            <tr>
                <td><?= h($modelosAlternativas->id) ?></td>
                <td><?= h($modelosAlternativas->nome) ?></td>
                <td><?= h($modelosAlternativas->grupo_id) ?></td>
                <td><?= h($modelosAlternativas->ativo) ?></td>
                <td><?= h($modelosAlternativas->criado_em) ?></td>
                <td><?= h($modelosAlternativas->modificado_em) ?></td>
                <td><?= h($modelosAlternativas->culpado_novo_id) ?></td>
                <td><?= h($modelosAlternativas->culpado_modificacao_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'ModelosAlternativas', 'action' => 'view', $modelosAlternativas->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'ModelosAlternativas', 'action' => 'edit', $modelosAlternativas->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'ModelosAlternativas', 'action' => 'delete', $modelosAlternativas->id], ['confirm' => __('Are you sure you want to delete # {0}?', $modelosAlternativas->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Setores') ?></h4>
        <?php if (!empty($grupo->setores)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Nome') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Deletado') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->setores as $setores): ?>
            <tr>
                <td><?= h($setores->id) ?></td>
                <td><?= h($setores->nome) ?></td>
                <td><?= h($setores->grupo_id) ?></td>
                <td><?= h($setores->culpado_novo_id) ?></td>
                <td><?= h($setores->culpado_modificacao_id) ?></td>
                <td><?= h($setores->criado_em) ?></td>
                <td><?= h($setores->modificado_em) ?></td>
                <td><?= h($setores->ativo) ?></td>
                <td><?= h($setores->deletado) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Setores', 'action' => 'view', $setores->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Setores', 'action' => 'edit', $setores->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Setores', 'action' => 'delete', $setores->id], ['confirm' => __('Are you sure you want to delete # {0}?', $setores->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Usuarios') ?></h4>
        <?php if (!empty($grupo->usuarios)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Nome') ?></th>
                <th scope="col"><?= __('Email') ?></th>
                <th scope="col"><?= __('Senha') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Cargo Id') ?></th>
                <th scope="col"><?= __('Redefinir Senha Token') ?></th>
                <th scope="col"><?= __('Redefinir Senha Email Hash') ?></th>
                <th scope="col"><?= __('Redefinir Senha Timestamp') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Deletado') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->usuarios as $usuarios): ?>
            <tr>
                <td><?= h($usuarios->id) ?></td>
                <td><?= h($usuarios->nome) ?></td>
                <td><?= h($usuarios->email) ?></td>
                <td><?= h($usuarios->senha) ?></td>
                <td><?= h($usuarios->criado_em) ?></td>
                <td><?= h($usuarios->modificado_em) ?></td>
                <td><?= h($usuarios->grupo_id) ?></td>
                <td><?= h($usuarios->cargo_id) ?></td>
                <td><?= h($usuarios->redefinir_senha_token) ?></td>
                <td><?= h($usuarios->redefinir_senha_email_hash) ?></td>
                <td><?= h($usuarios->redefinir_senha_timestamp) ?></td>
                <td><?= h($usuarios->culpado_novo_id) ?></td>
                <td><?= h($usuarios->culpado_modificacao_id) ?></td>
                <td><?= h($usuarios->ativo) ?></td>
                <td><?= h($usuarios->deletado) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Usuarios', 'action' => 'view', $usuarios->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Usuarios', 'action' => 'edit', $usuarios->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Usuarios', 'action' => 'delete', $usuarios->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuarios->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Visitas') ?></h4>
        <?php if (!empty($grupo->visitas)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Usuario Id') ?></th>
                <th scope="col"><?= __('Checklist Id') ?></th>
                <th scope="col"><?= __('Grupo Id') ?></th>
                <th scope="col"><?= __('Loja Id') ?></th>
                <th scope="col"><?= __('Prazo') ?></th>
                <th scope="col"><?= __('Observacao') ?></th>
                <th scope="col"><?= __('Dt Encerramento') ?></th>
                <th scope="col"><?= __('Culpado Novo Id') ?></th>
                <th scope="col"><?= __('Culpado Modificacao Id') ?></th>
                <th scope="col"><?= __('Requerimento Localizacao') ?></th>
                <th scope="col"><?= __('Usuario Vinculado Id') ?></th>
                <th scope="col"><?= __('Ativo') ?></th>
                <th scope="col"><?= __('Deletado') ?></th>
                <th scope="col"><?= __('Criado Em') ?></th>
                <th scope="col"><?= __('Modificado Em') ?></th>
                <th scope="col"><?= __('Setores Excluidos') ?></th>
                <th scope="col"><?= __('Token Visualizar Publico') ?></th>
                <th scope="col"><?= __('Is Public') ?></th>
                <th scope="col"><?= __('Teve Agendamento Flag') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($grupo->visitas as $visitas): ?>
            <tr>
                <td><?= h($visitas->id) ?></td>
                <td><?= h($visitas->usuario_id) ?></td>
                <td><?= h($visitas->checklist_id) ?></td>
                <td><?= h($visitas->grupo_id) ?></td>
                <td><?= h($visitas->loja_id) ?></td>
                <td><?= h($visitas->prazo) ?></td>
                <td><?= h($visitas->observacao) ?></td>
                <td><?= h($visitas->dt_encerramento) ?></td>
                <td><?= h($visitas->culpado_novo_id) ?></td>
                <td><?= h($visitas->culpado_modificacao_id) ?></td>
                <td><?= h($visitas->requerimento_localizacao) ?></td>
                <td><?= h($visitas->usuario_vinculado_id) ?></td>
                <td><?= h($visitas->ativo) ?></td>
                <td><?= h($visitas->deletado) ?></td>
                <td><?= h($visitas->criado_em) ?></td>
                <td><?= h($visitas->modificado_em) ?></td>
                <td><?= h($visitas->setores_excluidos) ?></td>
                <td><?= h($visitas->token_visualizar_publico) ?></td>
                <td><?= h($visitas->is_public) ?></td>
                <td><?= h($visitas->teve_agendamento_flag) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Visitas', 'action' => 'view', $visitas->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Visitas', 'action' => 'edit', $visitas->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Visitas', 'action' => 'delete', $visitas->id], ['confirm' => __('Are you sure you want to delete # {0}?', $visitas->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
