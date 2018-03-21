$(function() {

    $('.toggle-all-panels').click(function() {
        var $this = $(this);

        $('.toggle-parent').each(function() {
            if ($(this).data('expandido') != $this.data('expandido')) {
                $(this).click();
            }
        });
        if ($this.data('expandido') == 1) {
            $this
                .data('expandido', 0)
                .html('<span class="fa fa-minus"></span>');
        } else {
            $this
                .data('expandido', 1)
                .html('<span class="fa fa-plus"></span>');
        }
    });

    $('.btn-open-modal-mapa').click(function() {
        var $this = $(this);
        var lat = $this.data('lat');
        var lng = $this.data('lng');
        var distancia = $this.data('distancia');
        var accuracy = $this.data('accuracy');

        $('#modal-map-resposta-text').html('A resposta foi marcada há <strong>'+distancia+'</strong> da Loja.');

        $('#modal-mapa-lat').val(lat);
        $('#modal-mapa-lng').val(lng);
        $('#modal-mapa-accuracy').val(accuracy);
        console.log({lat: lat, lng: lng});

        // mapAndMaker.map.setCenter({lat: lat, lng: lng});

    });

    $('#modal-mapa').on('shown.bs.modal', function (e) {
        var lat = $('#modal-mapa-lat').val();
        var lng = $('#modal-mapa-lng').val();
        var lojaLat = $('#modal-mapa-loja-lat').val();
        var lojaLng = $('#modal-mapa-loja-lng').val();
        var accuracy = $('#modal-mapa-accuracy').val();

        var lojaNome = $('#modal-mapa-loja-nome').val();

        var latLng = new google.maps.LatLng(lat, lng);
        var lojaLatLng = new google.maps.LatLng(lojaLat, lojaLng);

        var mapAndMaker = initMap();
        //mapAndMaker.map.setCenter(latLng);
        // Marker resposta
        mapAndMaker.marker.setPosition(latLng);
        // var windowResposta = new google.maps.InfoWindow({
        //     content: 'Local da resposta'
        // });
        // windowResposta.open(mapAndMaker.map, mapAndMaker.marker);

        // Marker Loja
        mapAndMaker.markerLoja.setPosition(lojaLatLng);
        // var windowLoja = new google.maps.InfoWindow({
        //     content: lojaNome
        // });
        // windowLoja.open(mapAndMaker.map, mapAndMaker.markerLoja);

        var bounds = new google.maps.LatLngBounds();
        bounds.extend(mapAndMaker.marker.position);
        bounds.extend(mapAndMaker.markerLoja.position);
        //mapAndMaker.map.setZoom(18);
        mapAndMaker.map.fitBounds(bounds);

        console.log('ACCURACY', accuracy);

        // Maior que 10 pq menos de 10 vai dar um circulo tão pequeno que nem
        // vale a pena mostrar
        if (parseInt(accuracy) > 10) {
            var circle = new google.maps.Circle({
                map: mapAndMaker.map,
                center: mapAndMaker.marker.position,
                radius: parseInt(accuracy),
                strokeWeight: 1,
                strokeOpacity: 1,
                strokeColor: '#EF4836',
                fillColor: '#EF4836',
                fillOpacity: .5
            });
        }

        //mapAndMaker.map.setZoom(5);

    });

});


function initMap() {

    var map = new google.maps.Map(document.getElementById('map'));
    // var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        visible: true,
        anchorPoint: new google.maps.Point(0, -29),
        draggable: false,
        icon: 'http://retiro.kotacao.com.br/img/icon-check_48x48.png'
    });

    var markerLoja = new google.maps.Marker({
        map: map,
        visible: true,
        anchorPoint: new google.maps.Point(0, -29),
        draggable: false,
        icon: 'http://retiro.kotacao.com.br/img/icon-map-marker_48x48.png'
    });

    return {map: map, marker: marker, markerLoja: markerLoja};
}
