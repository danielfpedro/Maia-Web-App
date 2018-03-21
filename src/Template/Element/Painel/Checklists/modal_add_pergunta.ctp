<div id="my-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                <h4 class="modal-title">Pergunta</h4>
            </div>
            <div class="modal-body" style="background-color: #F5F5F5">

                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs  font-title-tab nav-justified" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#pergunta-tab" data-toggle="tab">
                                <span class="fa fa-comment"></span> Dados da Pergunta
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#imagens-tab" aria-controls="profile10" role="tab" data-toggle="tab">
                              <span class="fa fa-images"></span> Imagens Referência (<span class="modal-pergunta-text-total-imagens"></span>)
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content color4-bg" >
                        <div role="tabpanel" class="tab-pane active" id="pergunta-tab" style="padding-right: 0px;">
                            <div class="form-group modal-pergunta-setor-static-form-group" style="display: none;">
                                <label class="">Setor</label>
                                <p class="form-control-static modal-pergunta-setor-static">-</p>
                            </div>
                            <div class="form-group">
                                <label for="setores">Setor</label>
                                <select class="form-control" id="setores">
                                    <?php foreach ($setores as $id => $setor): ?>
                                        <option value="<?= (int)$id ?>"><?= h($setor) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="hidden" class="form-control" name="id" id="id">
                                <label for="pergunta" class="">Pergunta</label>
                                <textarea id="pergunta" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="questao-tipo">Tipo</label>
                                        <select class="form-control" id="questao-tipo">
                                            <option value="1">Multipla Escolha</option>
                                            <option value="2">Dissertativa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modelo-alternativas">Modelo de Alternativas</label>
                                        <select class="form-control" id="modelo-alternativas">
                                            <option value="">Nenhum</option>
                                            <?php foreach ($modelosAlternativas as $key => $modelo): ?>
                                                <option value="<?= (int)$modelo['id'] ?>" data-alternativas="<?= str_replace('"', '&quot;', json_encode($modelo['alternativas_dos_modelos'])) ?>"><?= h($modelo['nome']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="wrap-container-opcoes">

                                <div class="container-opcoes" style="display: none;">
                                    <div class="form-group">
                                        <!-- <label for="">Alternativas</label> -->
                                        <table class="table table-condensed table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 10px">
                                                        Alternativa
                                                    </th>
                                                    <th style="width: 120px; font-size: 10px;">
                                                        Valor
                                                    </th>
                                                    <th style="width: 50px">
                                                        <span class="fa fa-camera has-tooltip btn-toggle-checkbox-tem-foto" data-checked="0" title="Foto obrigatória" data-toggle="tooltip"></span>
                                                    </th>
                                                    <th style="width: 50px">
                                                        <span class="fa fa-exclamation-triangle has-tooltip btn-toggle-checkbox-item-critico" data-checked="0" title="Item crítico" data-toggle="tooltip"></span>
                                                    </th>
                                                    <!-- <th style="width: 25px"></th> -->
                                                    <th style="width: 25px"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="kode-alert kode-alert-icon alert-default-light" style="clear: both; margin-top: 10px;">
                                    <span class="fa fa-info-circle"></span>
                                    Você pode criar alternativas com o <strong>VALOR NULO</strong>.
                                </div>
                                <div class="kode-alert kode-alert-icon alert-default-light modal-pergunta-alert-ordenar" style="clear: both; margin-top: 10px;">
                                    <span class="fa fa-info-circle"></span>
                                    Você pode <strong>ORDENAR ALTERNATIVAS</strong> arrastando para cima ou para baixo.
                                </div>

                                <div class="text-center" style="margin: 30px 0 15px 0" style="display: none;">
                                    <button type="button" class="btn btn-danger btn-sm add-opcao">
                                        <span class="fa fa-plus"></span> Adicionar Alternativa
                                    </button>
                                </div>
                            </div>
                        </div>
                    <div role="tabpanel" class="tab-pane" id="imagens-tab">
                          <input id="fileupload" class="form-control" type="file" accept="image/jpeg, image/png" multiple/>
                          <br>

                          <div class="legenda-carregamento"></div>

                          <div class="" id="container-carrega-imagens">
                              <table class="table table-condensed table-striped">
                                  <tbody>
                                      <tr id="bloco-imagem-checklist" class="bloco-imagem" style="display: none">
                                          <td style="width: 80px;">
                                              <a href="" class="bloco-imagem-a">
                                                  <img src="" class="bloco-imagem-imagem" style="background-color: #EEE; height: 80px; width: 80px;">
                                              </a>
                                          </td>
                                          <td>
                                              <textarea placeholder="Legenda..." style="height: 80px;" class="form-control checklist-legenda-imagem"></textarea>
                                          </td>
                                          <td style="width: 50px;" class="text-center">
                                              <button type="button" class="bnt btn-light btn-xs btn-icon btn-checklist-remove-imagem" style="margin-top: 30px;"><span class="fa fa-times"></span></button>
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                          </div>
                    </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light pull-left" data-dismiss="modal">Cancelar</button>
                <!-- <button type="button" class="btn btn-default btn-salvar-pergunta" data-fechar="1"><span class="fa fa-check"></span> Salvar e Fechar</button> -->
                <button type="button" class="btn btn-default btn-salvar-pergunta"><span class="fa fa-check"></span> Salvar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
