<?php

echo $this->Html->script('../lib/blueimp-file-upload/js/jquery.iframe-transport', ['block' => true]);
echo $this->Html->script('../lib/blueimp-file-upload/js/jquery.fileupload', ['block' => true]);

echo $this->Html->css('../lib/lightbox2/dist/css/lightbox.min', ['block' => true]);
echo $this->Html->script('../lib/lightbox2/dist/js/lightbox.min', ['block' => true]);
echo $this->Html->script('Painel/lightbox_pt-br', ['block' => true]);

$title = __('Perguntas de "{0}"', $checklist->nome);
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    $this->Html->script('Painel/questoes', ['block' => true]);
?>

<?php echo $this->start('breadcrumbButtonsRight') ?>
    <button type="button" class="btn btn-light btn-importar-perguntas" data-nao-reabilita="<?= ($totalVisitasEncerradas > 0) ? 'true' : 'false' ?>" data-toggle="modal" data-target="#modal-importar">
        <span class="fa fa-download"></span> Importar Perguntas
    </button>
    <button type="button" class="btn btn-light btn-ordenar-setores" data-ordenando="0">
        <span class="fa fa-arrows-alt-v"></span> Ordenar Setores
    </button>
    <button type="button" class="btn btn-danger btn-open-modal-pergunta" data-nao-reabilita="<?= ($totalVisitasEncerradas > 0) ? 'true' : 'false' ?>" data-toggle="modal" data-target="#my-modal">
        <span class="fa fa-plus"></span> Adicionar Pergunta
    </button>
<?php echo $this->end() ?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Questionários' => $breadcrumb['index'],
    ]
]) ?>


<!-- URLS -->
<input type="hidden" id="url-carrega-perguntas" value="<?= $this->Url->build(['controller' => 'Checklists', 'action' => 'perguntas', '_ext' => 'json', 'checklistId' => (int)$checklist->id]) ?>">
<input type="hidden" id="url-remover-pergunta" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'delete', 'checklistId' => (int)$checklist->id, 'perguntaId' => '{{perguntaId}}', '_ext' => 'json']) ?>">
<input type="hidden" id="url-reordenar-perguntas" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'reordenar', '_ext' => 'json', 'checklistId' => (int)$checklist->id]) ?>">

<input type="hidden" id="url-add-pergunta" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'add', 'checklistId' => (int)$checklist->id, '_ext' => 'json']) ?>">
<input type="hidden" id="url-edit-pergunta" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'edit', 'checklistId' => (int)$checklist->id, 'perguntaId' => '{{perguntaId}}', '_ext' => 'json']) ?>">

<input type="hidden" id="url-reordenar-setores" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntasSetoresOrdem', 'action' => 'atualiza', '_ext' => 'json', 'checklistId' => (int)$checklist->id]) ?>">

<input type="hidden" id="url-upload-imagem" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntasImagens', 'action' => 'upload', 'checklistId' => (int)$checklist->id, '_ext' => 'json']) ?>">
<input type="hidden" id="url-deleta-imagem" value="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'delete', 'checklistId' => (int)$checklist->id, 'perguntaId' => '{{perguntaId}']) ?>">

<input type="hidden" id="url-webroot" value="<?= $this->request->webroot ?>">
<input type="hidden" id="static-files-base" value="<?= env('STATIC_FILES_BASE') ?>">
<input type="hidden" id="csrf-token" value="<?= $this->request->getParam('_csrfToken')?>">

<input type="hidden" id="total-visitas-encerradas" value="<?= (int)$totalVisitasEncerradas ?>">

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-12">

    <?php if ($totalVisitasEncerradas > 0): ?>
        <div class="kode-alert kode-alert-icon alert-default-light" style="margin-bottom: 20px;">
            <i class="fa fa-info-circle"></i>&nbsp;Este Questionário possui visitas ligadas a ele já encerradas. A edição das perguntas <strong>SERÁ LIMITADA</strong>.
        </div>
    <?php endif; ?>

      <div class="panel panel-default">
            <div class="panel-title">
                <span style="font-size: 18px; font-weight: normal;"><i class="fa fa-file-alt"></i>&nbsp;&nbsp;<?= $checklist->nome ?> <small class="panel-checklist-total-perguntas">(- perguntas)</small></span>
                <ul class="panel-tools">
                    <li>
                        <button type="button" class="icon toggle-all-panels" data-expandido="0"><span class="fa fa-plus"></span></button>
                    </li>
                </ul>
            </div>
        </div>

          <?= $this->element('Painel/Checklists/proto_buttons_ordenar_setores') ?>

          <?= $this->element('Painel/Checklists/proto_setor') ?>
          <?= $this->element('Painel/Checklists/proto_pergunta_panel') ?>

          <div id="carrega-conteudo">
              <div class="perguntas-loader text-center" style="margin: 30px 0">
                  <div class="spinner">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                  </div>
              </div>
          </div>

          <div id="msg-sem-perguntas" class="panel panel-default" style="display: none;">
            <div class="panel-body text-center">
              <h5>Esta Checklist não possui nenhum pergunta.</h5>
              <button type="button" class="btn btn-danger btn-open-modal-pergunta" data-toggle="modal" data-target="#my-modal">
                  <span class="fa fa-plus"></span> Adicionar Pergunta
              </button>
            </div>
          </div>

    </div>
</div>

<?= $this->element('Painel/Checklists/modal_add_pergunta') ?>
<?= $this->element('Painel/Checklists/modal_mover_pergunta') ?>

<div id="modal-importar" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Importar Perguntas</h4>
            </div>
            <form id="modal-importar-form" action="<?= $this->Url->build(['controller' => 'ChecklistsPerguntas', 'action' => 'importar', 'checklistId' => (int)$checklist->id, '_ext' => 'json'])?>">
                <input type="hidden" name="_csrfToken" value="<?= $this->request->getParam('_csrfToken')?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="label-control">Questionário</label>
                        <input
                            type="text"
                            name=""
                            value=""
                            placeholder="Pesquise pelo nome do Questionário..."
                            class="form-control" id="autocomplete-checklists"
                            data-url="<?= $this->Url->build(['controller' => 'Checklists', 'action' => 'autocompleteImportar', 'checklistId' => (int)$checklist->id]) ?>">
                    </div>

                    <ul id="modal-importar-carrega-perguntas" style="display: none; padding: 0;">
                        <!-- <li class="list-toggle">
                            <div class="checkbox"><input type="checkbox" id="list-toggle"><label for="list-toggle">&nbsp;</label></div>
                        </li> -->
                    </ul>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light pull-left" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-default btn-importar"><span class="fa fa-check"></span> Importar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="alert-success-bottom-right" class="kode-alert alert3 alert-custom kode-alert-bottom-right" style="display: none;z-index: 99999">
    <h4>Sucesso!</h4>
    <p>A pergunta foi gravada.</p>
</div>
<div id="alert-error-bottom-right" class="kode-alert alert6 alert-custom kode-alert-bottom-right" style="display: none;z-index: 99999">
    <h4>Erro!</h4>
    <p>-</p>
</div>
