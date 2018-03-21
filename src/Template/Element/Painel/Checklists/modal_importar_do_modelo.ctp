<div id="modal-importar-do-modelo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                <h4 class="modal-title">Criar Questionário do Modelo</h4>
            </div>

            <div class="modal-body">

                <form id="modal-importar-form" action="<?= $this->Url->build(['controller' => 'Checklists', 'action' => 'criarDoModelo', 'checklistId' => '{{checklistId}}']) ?>" method="POST">
                    <input type="hidden" name="_csrfToken" value="<?= $this->request->param('_csrfToken') ?>">
                    <div class="form-group">
                        <label for="modelos">Modelos</label>
                        <select
                            class="form-control"
                            id="modelos"
                            data-url-carrega-modelos="<?= $this->Url->build(['controller' => 'Checklists', 'action' => 'carregaModelos']) ?>"
                            data-url-carrega-perguntas="<?= $this->Url->build(['controller' => 'Checklists', 'action' => 'carregaPerguntas', 'checklistId' => '{{checklistId}}']) ?>">
                        </select>
                    </div>

                    <div class="form-group importar-modelo-loader" style="display: none;">
                        Carregando...
                    </div>

                    <div class="modal-importar-carrega-perguntas">

                    </div>

            </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light pull-left btn-modal-importar-modelo-fechar" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-default btn-importar-modelo"><span class="fa fa-check"></span> Importar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
