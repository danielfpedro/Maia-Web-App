<div class="row">
    <?php if ($isPublic): ?>
        <div class="col-lg-12">
    <?php else: ?>
        <div class="col-lg-6 col-lg-offset-3 col-md-12">
    <?php endif; ?>
        <div class="row">
            <div class="col-lg-12">
                <?php if ($isPublic): ?>
                    <h5>Auditor <strong><?= h($visita->usuario->short_name) ?></strong>, visita na loja <strong><?= h($visita->loja->nome) ?></strong> encerrada em <strong><?= $visita->dt_encerramento->format('d/m/Y \à\s H:i') ?></strong></h5>
                <?php endif ?>
                <div class="panel panel-default">
                    <div class="panel-title">
                        <span style="font-size: 20px; font-weight: normal;"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;<?= $visita->checklist->nome ?></span>
                        <ul class="panel-tools">
                            <li>
                                <button type="button" class="icon toggle-all-panels" data-expandido="1"><i class="fa fa-plus"></i></button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-title">
                        Obervação Geral
                        <ul class="panel-tools">
                            <li>
                                <button
                                    type="button"
                                    class="toggle-parent"
                                    data-plus-icon="fa fa-plus"
                                    data-minus-icon="fa fa-minus"
                                    data-expandido="1"
                                    data-esconder-selector=".panel-body"
                                    data-parent-selector=".panel">
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <p><?= ($visita->observacao) ? h($visita->observacao) : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?php $perguntasCounter = 1 ?>
                <?php foreach ($visita->checklist->setores as $key => $setor): ?>
                    <?php if ($setor->respondido): ?>

                        <!-- Titulo do Setor -->
                        <div class="panel panel-default">
                            <div class="panel-title">
                                <?= $setor->nome ?>&nbsp;
                                <?php if (!$isPublic): ?>
                                    <span class="<?= ($setor->diferenca > 0) ? 'color7' : 'color10' ?>" style="font-weight: normal"> - <?= (int)$setor->atingido ?> / <?= (int)$setor->maximo_possivel ?> pts (<?= (int)$setor->atingido_porcentagem ?>% <span class="fa fa-caret-<?= ($setor->diferenca > 0) ? 'up' : 'down' ?>"></span> <?= abs($setor->diferenca) ?>)</span>
                                <?php endif; ?>
                                <?php if ($setor->tem_observacoes || $setor->tem_fotos_requeridas || $setor->tem_resposta_critica): ?>
                                    &nbsp;|&nbsp;
                                    <?php if ($setor->tem_observacoes): ?>
                                        <span class="fa fa-commenting" data-toggle="tooltip" title="O auditor fazer um comentário em uma ou mais perguntas deste setor"></span>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($setor->tem_fotos_requeridas): ?>
                                        <span class="fa fa-camera" data-toggle="tooltip" title="O auditor adicionou fotos em uma ou mais perguntas deste setor"></span>&nbsp;
                                    <?php endif; ?>
                                    <?php if ($setor->tem_resposta_critica): ?>
                                        <span class="fa fa-warning" data-toggle="tooltip" title="O Setor possui uma ou mais perguntas respondidas com item crítico"></span>&nbsp;
                                    <?php endif; ?>
                                <?php endif; ?>
                                <ul class="panel-tools">
                                    <li>
                                        <button
                                            type="button"
                                            class="toggle-parent"
                                            data-plus-icon="fa fa-plus"
                                            data-minus-icon="fa fa-minus"
                                            data-expandido="0"
                                            data-esconder-selector=".panel-body"
                                            data-parent-selector=".panel">
                                        </button>
                                    </li>
                                </ul>

                                <br>

                                <span class="text-muted;" style="font-weight: normal;text-transform: none">
                                    <span class="fa fa-clock-o"></span> Respostas de <u><?= $setor->primeiraResposta->dt_resposta->format('d/m/y \à\s H:i:s') ?></u> até <u><?= $setor->ultimaResposta->dt_resposta->format('d/m/y \à\s H:i:s') ?></u> com duração de <u><?= $this->Text->toList($visita->getDuracaoString($setor->duracao)) ?></u>
                                </span>
                            </div>
                            <div class="panel-body">
                                <?php foreach ($setor->perguntas as $keyPergunta => $pergunta): ?>

                                  <div class="panel panel-default pergunta">
                                      <div class="panel-title" style="font-size: 15px;">
                                          <span class="panel-pergunta-text-counter">#<?= $perguntasCounter ?></span>
                                          <?php $perguntasCounter++; ?>
                                      </div>

                                      <div class="panel-body">

                                          <p class="panel-pergunta-text-pergunta" style="font-size: 17px; font-weight: bold; padding-top: 10px;">
                                              <?= $pergunta->pergunta ?>
                                          </p>

                                          <?php if ($pergunta['tipo'] == 1): ?>
                                              <ul class="list-group">
                                                  <li class="list-group-item">
                                                      <div class="row" style="font-weight: bold;">
                                                          <div class="col-md-4">
                                                              Alternativa
                                                          </div>
                                                          <div class="col-md-2 text-center">
                                                              Valor
                                                          </div>
                                                          <div class="col-md-2">
                                                              <span class="fa fa-camera"></span>
                                                          </div>
                                                          <div class="col-md-2">
                                                              <span class="fa fa-warning"></span>
                                                          </div>
                                                          <div class="col-md-2" title="Crítico Resolvido">
                                                              <span class="fa fa-warning"></span>
                                                              <span class="fa fa-check"></span>
                                                          </div>
                                                      </div>
                                                  </li>
                                                  <?php foreach ($pergunta->alternativas as $keyAlternativa => $alternativa): ?>
                                                      <li class="list-group-item <?= ($alternativa->selecionada) ? 'color7' : '' ?>">
                                                          <div class="row">
                                                            <div class="col-md-4 panel-pergunta-li-alternativa-alternativa">
                                                                <?= h($alternativa->alternativa) ?>
                                                                <?php if ($alternativa->selecionada): ?>
                                                                    &nbsp;<span class="fa fa-check"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-2 text-center panel-pergunta-li-alternativa-valor">
                                                                <?= (int)$alternativa->valor ?>
                                                            </div>
                                                            <div class="col-md-2 color10">
                                                                <?= ($alternativa->tem_foto) ? '<span class="fa fa-check"></span>' : '' ?>
                                                            </div>
                                                            <div class="col-md-2 color10">
                                                                <?= ($alternativa->item_critico) ? '<span class="fa fa-check"></span>' : '' ?>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <?php if ($alternativa->selecionada && $alternativa->item_critico): ?>
                                                                    <div class="checkbox checkbox-primary" style="margin: 0;margin-bottom: 5px;">
                                                                        <input
                                                                            id="critico-resolvido<?= (int)$pergunta->id ?>"
                                                                            type="checkbox"
                                                                            class="btn-save-resolvido"
                                                                            data-url="<?= $this->Url->build(['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'toggleCriticoResolvido', 'respostaId' => (int)$pergunta->resposta->id, 'method' => 'POST', '_ext' => 'json']) ?>"
                                                                            data-resposta-id="<?= (int)$pergunta->resposta->id

                                                                             ?>"
                                                                            <?= ((boolean)$pergunta->resposta->critico_resolvido) ? 'checked' : '' ?>>
                                                                        <label for="critico-resolvido<?= (int)$pergunta->id ?>">&nbsp;</label>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                          </div>
                                                      </li>
                                                  <?php endforeach; ?>
                                              </ul>
                                          <?php else: ?>
                                              <ul class="list-group">
                                                  <li class="list-group-item">
                                                      <em><?= h($pergunta->resposta->resposta_em_texto) ?></em>
                                                  </li>
                                              </ul>
                                          <?php endif; ?>

                                          <?php if ($pergunta->resposta && $pergunta->resposta->tem_localizacao): ?>
                                              <?php
                                                  $distanciaParaLoja = $visita->loja->getDistanceFromLoja($pergunta->resposta->lat, $pergunta->resposta->lng);
                                                  $distanciaParaLojaComUnidade = $visita->loja->getDistanceWithUnit($distanciaParaLoja);
                                                  $distanciaMaxima = 100;
                                              ?>

                                              <?php if (!$isPublic): ?>
                                                  <ul class="list-group">
                                                      <li class="list-group-item">
                                                            <button
                                                                type="button"
                                                                class="btn btn-xs btn-open-modal-mapa <?= ($distanciaParaLoja > $distanciaMaxima) ? 'btn-danger' : 'btn-default' ?>"
                                                                data-loja-lat="<?= (float)$visita->loja->lat ?>"
                                                                data-loja-lng="<?= (float)$visita->loja->lng ?>"
                                                                data-lat="<?= (float)$pergunta->resposta->lat ?>"
                                                                data-lng="<?= (float)$pergunta->resposta->lng ?>"
                                                                data-accuracy="<?= (float)$pergunta->resposta->location_accuracy ?>"
                                                                data-distancia="<?= $distanciaParaLojaComUnidade ?>"
                                                                data-toggle="modal"
                                                                data-target="#modal-mapa">
                                                                <span class="fa fa-map-marker"></span> Localização <?= ($distanciaParaLoja > $distanciaMaxima) ? '<span class="fa fa-warning" style="padding: 0; margin: 0;"></span>' : '' ?>
                                                            </button>
                                                        </li>
                                                    </ul>
                                              <?php endif; ?>
                                          <?php endif; ?>

                                          <?php if ($pergunta->resposta->observacao): ?>
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <em><?= h($pergunta->resposta->observacao) ?></em>
                                                </li>
                                            </ul>
                                          <?php endif; ?>
                                          <ul class="list-group">
                                              <li class="list-group-item text-muted">
                                                  <em>Respondida em <?= ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('d/m/y \à\s H:i:s') : '-' ?></em>
                                              </li>
                                          </ul>
                                      </div>
                                  </div>


                                    <!-- <div style="">
                                        <p><strong><?= $pergunta->pergunta ?></strong></p>
                                        <?php if ($pergunta['tipo'] == 1): ?>
                                            <ul style="margin-bottom: 10px; padding-bottom: 0;" class="list-unstyled">
                                                <?php foreach ($pergunta->alternativas as $keyAlternativa => $alternativa): ?>
                                                    <li class="<?= ($alternativa->selecionada) ? 'color7' : '' ?>">
                                                        <?= h($alternativa->alternativa) ?><?= (!$isPublic) ? '&nbsp(' . $alternativa->valor . ')' : null  ?>
                                                        <?php if ($alternativa->selecionada): ?>
                                                            &nbsp;<span class="fa fa-check"></span>&nbsp;
                                                        <?php endif; ?>
                                                        <?php if ($alternativa->item_critico): ?>
                                                            &nbsp;<span class="fa fa-warning color10"></span>&nbsp;
                                                        <?php endif; ?>
                                                        <?php if ($alternativa->selecionada): ?>
                                                            <p class="text-muted"><em><small> Respondida em <?= ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('d/m/y \à\s H:i:s') : '-' ?></small></em></p>
                                                        <?php endif; ?>
                                                        <?php if ($alternativa && $alternativa->item_critico && $this->request->action != 'viewPublic'): ?>
                                                            <?php if ($alternativa->selecionada): ?>
                                                                <div class="checkbox checkbox-primary" style="margin: 0;margin-bottom: 5px;">
                                                                    <input
                                                                        id="critico-resolvido<?= (int)$pergunta->id ?>"
                                                                        type="checkbox"
                                                                        class="btn-save-resolvido"
                                                                        data-url="<?= $this->Url->build(['controller' => 'ChecklistsPerguntasRespostas', 'action' => 'toggleCriticoResolvido', 'respostaId' => (int)$pergunta->resposta->id, 'method' => 'POST', '_ext' => 'json']) ?>"
                                                                        data-resposta-id="<?= (int)$pergunta->resposta->id

                                                                         ?>"
                                                                        <?= ((boolean)$pergunta->resposta->critico_resolvido) ? 'checked' : '' ?>>
                                                                    <label for="critico-resolvido<?= (int)$pergunta->id ?>">
                                                                            Item crítico resolvido
                                                                    </label>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php if ($pergunta->resposta && $pergunta->resposta->observacao): ?>
                                                    <li>
                                                        <strong>Observação:</strong> <?= h($pergunta->resposta->observacao) ?>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p style=""><?= h($pergunta->resposta->resposta_em_texto) ?></p>
                                            <p class="text-muted"><em><small> Respondida em <?= ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('d/m/y \à\s H:i') : '-' ?></small></em></p>
                                        <?php endif; ?>

                                        <?php if ($pergunta->resposta && $pergunta->resposta->tem_localizacao): ?>
                                            <?php
                                                $distanciaParaLoja = $visita->loja->getDistanceFromLoja($pergunta->resposta->lat, $pergunta->resposta->lng);
                                                $distanciaParaLojaComUnidade = $visita->loja->getDistanceWithUnit($distanciaParaLoja);
                                                $distanciaMaxima = 100;
                                            ?>

                                            <?php if (!$isPublic): ?>
                                              <div style="">
                                                  <button
                                                      type="button"
                                                      class="btn btn-xs btn-open-modal-mapa <?= ($distanciaParaLoja > $distanciaMaxima) ? 'btn-danger' : 'btn-default' ?>"
                                                      data-loja-lat="<?= (float)$visita->loja->lat ?>"
                                                      data-loja-lng="<?= (float)$visita->loja->lng ?>"
                                                      data-lat="<?= (float)$pergunta->resposta->lat ?>"
                                                      data-lng="<?= (float)$pergunta->resposta->lng ?>"
                                                      data-accuracy="<?= (float)$pergunta->resposta->location_accuracy ?>"
                                                      data-distancia="<?= $distanciaParaLojaComUnidade ?>"
                                                      data-toggle="modal"
                                                      data-target="#modal-mapa">
                                                      <span class="fa fa-map-marker"></span> Localização <?= ($distanciaParaLoja > $distanciaMaxima) ? '<span class="fa fa-warning" style="padding: 0; margin: 0;"></span>' : '' ?>
                                                  </button>
                                              </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div class="container-horizontal-scroll">
                                            <?php if ($pergunta->resposta): ?>
                                                <?php foreach ($pergunta->resposta->fotos_requeridas as $foto): ?>
                                                    <a href="<?= $this->Url->build($foto->full_image_path) ?>" data-lightbox="<?= (int)$pergunta->id ?>" data-title="<?= ($foto->dt_que_foi_criada) ? $foto->dt_que_foi_criada->format('d/m/y \à\s H:i') : '' ?>">
                                                        <img src="<?= $this->Url->build($foto->full_image_quadrada_path) ?>" style="width: 90px; height: 90px;" class="img-rounded">
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?> -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
