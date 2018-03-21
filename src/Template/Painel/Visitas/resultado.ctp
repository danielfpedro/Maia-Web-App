<?php
    $title = __('Visita "{0} | {1}"', h($visita->cod), h($visita->loja->nome));
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);

    echo $this->Html->css('../lib/lightbox2/dist/css/lightbox.min', ['block' => true]);
    echo $this->Html->script('../lib/lightbox2/dist/js/lightbox.min', ['block' => true]);
    echo $this->Html->script('Painel/lightbox_pt-br', ['block' => true]);

    echo $this->Html->script('Painel/respostas_view', ['block' => true]);

    echo $this->Html->script('https://maps.googleapis.com/maps/api/js?key=AIzaSyCjhVwkKaBB5fcGVaM6yrsGBbytEu2PP7s&libraries=places&callback=', ['block' => true]);
    echo $this->Html->script('../lib/gmap3/dist/gmap3.min', ['block' => true, 'async', 'defer']);

?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Visitas' => $breadcrumb['index'],
    ]
]) ?>

<?= $this->element('Painel/ChecklistsPerguntasRespostas/respostas', ['isPublic' => false]) ?>  

<div id="modal-mapa" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <input type="hidden" id="modal-mapa-distancia">
            <input type="hidden" id="modal-mapa-lat" value="">
            <input type="hidden" id="modal-mapa-lng" value="">
            <input type="hidden" id="modal-mapa-accuracy" value="">
            <input type="hidden" id="modal-mapa-loja-lat" value="<?= (float)$visita->loja->lat ?>">
            <input type="hidden" id="modal-mapa-loja-lng" value="<?= (float)$visita->loja->lng ?>">
            <div style="width: 100%; height: 500px; background: #EEE;" id="map"></div>

            <div class="" style="padding: 25px;">
                <div class="media">
                    <div class="media-left">
                        <a href="#">
                            <?= $this->Html->image('icon-map-marker.png', ['class' => 'media-object', 'width' => '70']) ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <?= h($visita->loja->nome) ?>
                        </h4>
                        <p>
                            <?= h($visita->loja->bairro) ?>, <?= h($visita->loja->endereco) ?>, <?= h($visita->loja->cidade->nome) ?> / <?= h($visita->loja->cidade->uf) ?>
                        </p>
                    </div>
                </div>
                <div class="media" style="margin-top: 20px;">
                    <div class="media-left">
                        <a href="#">
                            <?= $this->Html->image('icon-check.png', ['class' => 'media-object', 'width' => '70']) ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Local da Resposta <small id="modal-map-distancia-text"></small></h4>
                        <p id="modal-map-resposta-text"></p>
                    </div>
                </div>

                <?php if (!$visita->isRespostaPrecisa()): ?>
                    <p class="kode-alert kode-alert-icon alert6-light" style="margin-top: 30px;">
                        <span class="fa fa-warning"></span> A visita foi configurada para <strong>não exigir conexão com a Internet</strong> no momento da resposta e pode
                        resultar em uma imprecisão de até <strong>200 metros</strong> em relação a localização real da resposta.
                    </p>
                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
