$(function() {
    $('.btn-salvar').attr('disabled', true);
    $('#estados').preencheCidades({
        targetSelector: '#cidade-id',
        targetDisabledText: 'Selecione o Estado:',
        key: 'cidades',
        label: 'nome',
        source: $('#estados').data('url'),
        done: function() {
            $('.btn-salvar').attr('disabled', false);
        },
    });

});

$(function() {

    $('#cep').mask('99999-999');

    $('.btn-preenche-endereco').click(function() {
        var $this = $(this);
        var cep = $('#cep').val();

        if (cep) {
            $this.find('span').addClass('fa-spinner fa-spin');

            camposEnderecoDisabledToggle(true);

            $.getJSON($this.data('url'), {cep: cep}, function(data) {
                camposEnderecoDisabledToggle(false);
                if (data.result) {
                    console.log('Result', data.result);
                    $('#estados')
                        .val(data.result.cidade.estado_id)
                        .attr('data-preselect-city', data.result.cidade.id)
                        .change();

                    $('#bairro').val(data.result.bairro.nome);
                    $('#endereco')
                        .val(data.result.endereco + ', Nº ')
                        .focus();
                } else {
                    swal({
                        type: 'info',
                        title: 'Endereço não encontrado',
                        text: 'Nenhum endereço foi encontrado com o CEP informado.'
                    });
                }
            })
            .fail(function() {
                swal({
                    type: 'error',
                    title: 'Erro',
                    text: 'Ocorreu um erro ao buscar tentar buscar o endereço. Por favor, tente novamente.'
                });
                camposEnderecoDisabledToggle(false);
            })
            .always(function() {
                $this.find('span').removeClass('fa-spinner fa-spin');
            });
        }
    });
    function camposEnderecoDisabledToggle(flag){
        var elements = ['btn-preenche-endereco', 'cep', 'estados', 'cidade-id', 'bairro', 'endereco'];
        $.each(elements, function(i,val) {
            $('#' + val).attr('disabled', flag);
        });
    }


});

function initMap() {

var lat = $('#lat').val();
var lng = $('#lng').val();

if (lat && lng) {
    var initialPosition = new google.maps.LatLng(lat, lng);
} else {
    var initialPosition = new google.maps.LatLng(-14.2350, -51.9253);
    $('#lat').val();
    $('#lng').val();
}

  var map = new google.maps.Map(document.getElementById('map'), {
    center: initialPosition,
    zoom: 5
    });

    if (lat && lng) {
        map.setZoom(18);
    }


  var input = /** @type {!HTMLInputElement} */(
      document.getElementById('pac-input'));

  // var types = document.getElementById('type-selector');
  // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  // map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  // var infowindow = new google.maps.InfoWindow();
  var marker = new google.maps.Marker({
    map: map,
    position: initialPosition,
    visible: true,
    anchorPoint: new google.maps.Point(0, -29),
    draggable: true,
  });

  // var bounds = new google.maps.LatLngBounds();
  // bounds.extend(marker.getPosition());
  // map.fitBounds(bounds);

  // console.log('Marker', marker.getPosition());

  marker.addListener('dragend', function(event) {
      lat = event.latLng.lat();
      lng = event.latLng.lng();
      $('#lat').val(lat);
      $('#lng').val(lng);
      map.setCenter(event.latLng);
      console.log(event);
  });

  autocomplete.addListener('place_changed', function() {
    // infowindow.close();
    input.value = '';
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      window.alert("Local não encontrado.");
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    // marker.setIcon(/** @type {google.maps.Icon} */({
    //   url: place.icon,
    //   size: new google.maps.Size(71, 71),
    //   origin: new google.maps.Point(0, 0),
    //   anchor: new google.maps.Point(17, 34),
    //   scaledSize: new google.maps.Size(35, 35)
    // }));
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    $('#lat').val(place.geometry.location.lat());
    $('#lng').val(place.geometry.location.lng());

    // var address = '';
    //
    // if (place.address_components) {
    //   address = [
    //     (place.address_components[0] && place.address_components[0].short_name || ''),
    //     (place.address_components[1] && place.address_components[1].short_name || ''),
    //     (place.address_components[2] && place.address_components[2].short_name || '')
    //   ].join(' ');
    // }

    // infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    // infowindow.open(map, marker);
  });

  // Sets a listener on a radio button to change the filter type on Places
  // Autocomplete.
  // function setupClickListener(id, types) {
  //   var radioButton = document.getElementById(id);
  //   radioButton.addEventListener('click', function() {
  //     autocomplete.setTypes(types);
  //   });
  // }
  //
  // setupClickListener('changetype-all', []);
  // setupClickListener('changetype-address', ['address']);
  // setupClickListener('changetype-establishment', ['establishment']);
  // setupClickListener('changetype-geocode', ['geocode']);
}
