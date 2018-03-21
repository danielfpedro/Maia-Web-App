<?php if ($isPublic): ?>
  <h3><?= h($visita->cod) ?> | <?= h($visita->loja->nome) ?></h3>
<?php endif ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="">
            <ul class="topstats clearfix" style="padding: 0; margin: 0;">
                <li class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <span class="title"><i class="fa fa-user"></i> Auditor</span>
                    <h3><?= h($visita->usuario->short_name) ?></h3>
                </li>
                <li class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <span class="title"><i class="fa fa-dot-circle"></i> Máximo possível</span>
                    <h3><?= $visita->atingimento['maximo_possivel'] ?>pts</h3>
                </li>
                <li class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <span class="title"><i class="fa fa-dot-circle"></i> Mínimo esperado</span>
                    <h3><?= $visita->checklist->minimo_esperado ?>%</h3>
                </li>
                <li class="col-lg-<?= ($isPublic) ? '4' : '2' ?> col-sm-6 col-xs-12">
                    <span class="title"><i class="fa fa-dot-circle"></i> Atingido</span>
                    <h3 class="color-<?= ($visita->atingimento['diferenca'] < 0) ? 'down' : 'up' ?>"><?= $visita->atingimento['atingido'] ?>pts / <?= $visita->atingimento['atingido_porcentagem'] ?>%</h3>
                    <?php
                        if ($visita->atingimento['diferenca'] == 0) {
                            $textoMeta = 'Mínimo esperado atingido';
                        } else if($visita->atingimento['diferenca'] < 0) {
                            $textoMeta = 'abaixo do mínimo esperado';
                        } else {
                            $textoMeta = 'acima do mínimo esperado';
                        }
                    ?>
                    <span class="diff"><b class="color-<?= ($visita->atingimento['diferenca'] < 0) ? 'down' : 'up' ?>"><i class="fa fa-caret-<?= ($visita->atingimento['diferenca'] < 0) ? 'down' : 'up' ?>"></i> <?= abs($visita->atingimento['diferenca']) ?>%</b> <?= $textoMeta ?></span>
                </li>
                <?php if (!$isPublic): ?>
                  <li class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                      <span class="title"><i class="fa fa-calendar"></i> Prazo</span>
                      <h3><?= ($visita->prazo) ? $visita->prazo->format('d/m/y') : '-' ?></h3>
                  </li>
                <?php endif ?>
                <li class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <span class="title"><i class="fa fa-calendar"></i> Encerramento</span>
                    <h3><?= $visita->dt_encerramento->format('d/m/y') ?> <small>às <?= $visita->dt_encerramento->format('H:i') ?></small></h3>
                    <?php if (!$isPublic): ?>
                        <span class="diff"><i class="fa fa-clock"></i> Duração de <strong><?= $this->Text->toList($visita->getDuracaoString($visita->getDuracao())) ?></strong></span>
                    <?php endif ?>
                </li>
            </ul>
        </div>
    </div>
</div>

    <div class="row">
        <div class="<?= (!$isPublic) ? 'col-lg-6 col-lg-offset-3' : 'col-lg-12' ?>">
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
                    <p><?= ($visita->observacao) ? nl2br(h($visita->observacao)) : '-' ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="<?= (!$isPublic) ? 'col-lg-6 col-lg-offset-3' : 'col-lg-12' ?>">
            <?php $perguntasCounter = 1 ?>
            <?php foreach ($visita->checklist->setores as $setor): ?>
                <?php if ($setor->respondido): ?>
                    <!-- Titulo do Setor -->
                    <div class="panel panel-default">
                        <div class="panel-title">
                            <?= h($setor->nome) ?>&nbsp;
                            <span class="<?= ($setor->diferenca >= 0) ? 'color7' : 'color10' ?>" style="font-weight: normal"> - <?= (int)$setor->atingido ?> / <?= (int)$setor->maximo_possivel ?> pts (<?= (int)$setor->atingido_porcentagem ?>% <span class="fa fa-caret-<?= ($setor->diferenca >= 0) ? 'up' : 'down' ?>"></span> <?= number_format(abs($setor->diferenca), 0) ?>)</span>
                            <?php if ($setor->tem_observacoes || $setor->tem_fotos_requeridas || $setor->tem_resposta_critica): ?>
                                &nbsp;|&nbsp;
                                <?php if ($setor->tem_observacoes): ?>
                                    <span class="fa fa-comments" data-toggle="tooltip" title="O auditor fazer um comentário em uma ou mais perguntas deste setor"></span>&nbsp;
                                <?php endif; ?>
                                <?php if ($setor->tem_fotos_requeridas): ?>
                                    <span class="fa fa-camera" data-toggle="tooltip" title="O auditor adicionou fotos em uma ou mais perguntas deste setor"></span>&nbsp;
                                <?php endif; ?>
                                <?php if ($setor->tem_resposta_critica): ?>
                                    <span class="fa fa-exclamation-triangle" data-toggle="tooltip" title="O Setor possui uma ou mais perguntas respondidas com item crítico"></span>&nbsp;
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

                            <?php if (!$isPublic): ?>
                              <span class="text-muted;" style="font-weight: normal;text-transform: none">
                                  <span class="fa fa-clock"></span> Respostas de <u><?= ($setor->primeiraResposta) ? $setor->primeiraResposta->dt_resposta->format('d/m/y \à\s H:i:s') : '-' ?></u> até <u><?= ($setor->ultimaResposta) ? $setor->ultimaResposta->dt_resposta->format('d/m/y \à\s H:i:s') : '' ?></u> com duração de <u><?= $this->Text->toList($visita->getDuracaoString($setor->duracao)) ?></u>
                              </span>
                            <?php endif ?>
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
                                                      <?php if (!$isPublic): ?>
                                                          <div class="col-md-2 text-center">
                                                              Valor
                                                          </div>
                                                          <div class="col-md-2 text-center">
                                                              <span class="fa fa-camera"></span>
                                                          </div>
                                                          <div class="col-md-2 text-center">
                                                              <span class="fa fa-exclamation-triangle"></span>
                                                          </div>
                                                          <div class="col-md-2 text-center" title="Crítico Resolvido">
                                                              <span class="fa fa-exclamation-triangle"></span>
                                                              <span class="fa fa-check"></span>
                                                          </div>
                                                      <?php endif; ?>
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
                                                        <?php if (!$isPublic): ?>
                                                            <div class="col-md-2 text-center panel-pergunta-li-alternativa-valor">
                                                                <?= (!is_null($alternativa->valor)) ? (int)$alternativa->valor : '-' ?>
                                                            </div>
                                                            <div class="col-md-2 text-center <?= ($alternativa->tem_foto) ? 'color10' : 'color3' ?>">
                                                                <span class="fa fa-<?= ($alternativa->tem_foto) ? 'check' : 'minus' ?>"></span>
                                                            </div>
                                                            <div class="col-md-2 text-center <?= ($alternativa->item_critico) ? 'color10' : 'color3' ?>">
                                                                <span class="fa fa-<?= ($alternativa->item_critico) ? 'check' : 'minus' ?>"></span>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <?php if ($alternativa->selecionada && $alternativa->item_critico): ?>
                                                                    <div class="checkbox checkbox-primary" style="margin: 0; padding: 0;margin-left: 30px;">
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
                                                                <?php else: ?>
                                                                    <span class="fa fa-minus color3"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php endif; ?>
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

                                      <?php if ($pergunta->resposta && $pergunta->resposta->tem_localizacao && !$isPublic): ?>
                                          <?php
                                              $distanciaParaLoja = $visita->loja->getDistanceFromLoja($pergunta->resposta->lat, $pergunta->resposta->lng);
                                              $distanciaParaLojaComUnidade = $visita->loja->getDistanceWithUnit($distanciaParaLoja);
                                              $distanciaMaxima = 100;
                                          ?>

                                          <?php if (!$isPublic): ?>
                                              <ul class="list-group">
                                                  <li class="list-group-item text-center" style="padding: 35px 10px;">
                                                        <button
                                                            type="button"
                                                            class="btn btn-open-modal-mapa <?= ($distanciaParaLoja > $distanciaMaxima) ? 'btn-danger' : 'btn-default' ?>"
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

                                      <?php if ($pergunta->resposta->fotos_requeridas): ?>
                                          <ul class="list-group">
                                              <li class="list-group-item text-muted">
                                                  <div class="container-horizontal-scroll">
                                                      <?php foreach ($pergunta->resposta->fotos_requeridas as $foto): ?>
                                                          <a href="<?= $this->Url->build(env('STATIC_FILES_BASE') . $foto->full_image_path) ?>" data-lightbox="<?= (int)$pergunta->id ?>" data-title="<?= ($foto->dt_que_foi_tirada) ? $foto->dt_que_foi_tirada->format('d/m/y \à\s H:i') : '' ?>">
                                                              <?= $this->Html->image('../' . $foto->full_image_quadrada_path, ['style' => 'width: 90px; height: 90px;', 'class' => 'img-rounded']) ?>
                                                          </a>
                                                      <?php endforeach; ?>
                                                  </div>
                                              </li>
                                          </ul>
                                      <?php endif; ?>

                                      <?php if ($pergunta->resposta->observacao): ?>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <em><?= nl2br(h($pergunta->resposta->observacao)) ?></em>
                                            </li>
                                        </ul>
                                      <?php endif; ?>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                Respondida em <u><?= ($pergunta->resposta->dt_resposta) ? $pergunta->resposta->dt_resposta->format('d/m/y \à\s H:i:s') : '-' ?></u>
                                            </li>
                                        </ul>
                                  </div>
                              </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
