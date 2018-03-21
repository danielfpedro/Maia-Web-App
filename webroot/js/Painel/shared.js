$(function() {
    $('.open-modal-texto').click(function() {
        var $this = $(this);
        $('#modal-texto').find('.modal-title').text($this.data('title'));
        $('#modal-texto').find('.modal-body > p').html($this.data('texto'));
    });

    $('.btn-delete').click(function() {
        var $this = $(this);
        var url = $this.attr('href');

        swal({
                type: 'info',
              title: 'Atenção!',
              html: 'Você realmente deseja remover esta visita?',
              showCancelButton: true,
              confirmButtonColor: '#F15949',
              confirmButtonText: 'Remover Visita',
              showLoaderOnConfirm: true,
              preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.post(url + '.json', {_csrfToken: $('body').data('csrf-token')}, function() {
                        resolve();
                    })
                    .error(function(error) {
                        console.log(error.responseJSON.message);
                        reject(error.responseJSON.message);
                    });
                });
              },
              allowOutsideClick: false
            }).then(function (email) {
              swal({
                type: 'success',
                title: 'Sucesso!',
                html: 'A visita foi removida, Por favor, aguarde...',
                showConfirmButton: false
                });
                window.setTimeout(function() {
                    location.reload();
                }, 1500);
          });
        return false;
    });

    $('.btn-critical-delete').click(function() {
        var $this = $(this);
        var url = $this.attr('href');

        swal({
                type: 'info',
              title: 'Atenção!',
              html: 'Esta ação <strong>NÃO PODE SER DESFEITA</strong>! Esta visita já foi encerrada e possui resultados que impactam em relatórios e serão perdidos com a remoção.<br><br>Insira a sua senha no campo abaixo para confirmar a remoção da visita.',
              input: 'password',
              inputPlaceholder: 'Senha',
              showCancelButton: true,
              confirmButtonColor: '#F15949',
              confirmButtonText: 'Eu entendo as consequências, REMOVER VISITA',
              showLoaderOnConfirm: true,
              preConfirm: function (senha) {
                return new Promise(function (resolve, reject) {
                    $.post(url + '.json', {senha: senha, _csrfToken: $('body').data('csrf-token')}, function() {
                        resolve();
                    })
                    .error(function(error) {
                        console.log(error.responseJSON.message);
                        reject(error.responseJSON.message);
                    });
                });
              },
              allowOutsideClick: false
            }).then(function (email) {
              swal({
                type: 'success',
                title: 'Sucesso!',
                html: 'A visita foi removida, Por favor, aguarde...',
                showConfirmButton: false
                });
                window.setTimeout(function() {
                    location.reload();
                }, 1500);
          });
        return false;
    });

    $(".select2").select2();
    $(".select2tag").select2({
        tags: true
    });

    // Se tiver desabilitado tenho que fazer isso
    $('li.disabled > form').next('a').attr('onClick', null);
    $('li.disabled > a').click(function(e) {
        e.preventDefault();
        return false;
    });

    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

    $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $('.dont-submit-form').keydown(function(e) {
        if((e.keyCode == 13)) {
            e.preventDefault();
            return false;
        }
    });

    // panel toggle
    $('.panel').each(function() {
        var $this = $(this);
        if ($this.data('minimized') == 1) {
            $this.find('.panel-body').hide();
        }
    });
    function togglePanel($this) {
        $this.parents('.panel').find('.panel-body').slideToggle();
    }
    $(document).on('click', '.toggle-parent', function() {
        toggleParent($(this));
    });

    $(document).on('click', '.toggle-panel', function() {
        togglePanel($(this));
    })

    // Fecha o alert
    window.setTimeout(function() {
        $('.alert-autoclose').fadeOut('slow');
    }, 3000);

    $('.date')
        .attr('autocomplete', 'off')
        .css('cursor', 'pointer')
        .mask('99/99/9999')
        .datepicker({
            dateFormat: 'dd/mm/yy',
            altFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            showOptions: 'focus'
        });
    $('.date').keydown(function(e){
        // Aqui temos um gambi... eu tenho a mascara que só deixa entrada de numero
        // mas eu não quero que ele dgita nada entao eu coloquei a regex pra letra...
        // como a mascara já nao deixa digitar letra ele nao digita nada... o bom
        // é que ele nao rejeita nenhum "F1, F2..." e control entao consigo por exemplo
        // dar um control f5 enquanto o foco está neste input;
        var regex = new RegExp("^[a-z]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    /**
     * Filtro extra
     */
    if (window.location.hash == '#filtro-extra') {
        $('.filtro-extra').show();
        $('.filtro-extra-toggle').find('.fa').addClass('fa-chevron-up');
        $('.filtro-extra-toggle').find('.fa').removeClass('fa-chevron-down');
    }
    $('.filtro-extra-toggle').click(function(e) {
        var $this = $(this);
        if ($this.find('.fa').hasClass('fa-chevron-down')) {
            $this.find('.fa').addClass('fa-chevron-up');
            $this.find('.fa').removeClass('fa-chevron-down');

            window.location.hash = 'filtro-extra';
        } else {
            $this.find('.fa').removeClass('fa-chevron-up');
            $this.find('.fa').addClass('fa-chevron-down');

            window.location.hash = '';
        }
        $('.filtro-extra').slideToggle('fast');

        e.preventDefault();
    });

    /**
     * Modal
     */
     $('.abre-modal-ajax').click(function() {
        var $this = $(this);
        var url = $this.attr('href');
        
        $modal = $($this.data('modal-target'));

        $modal.modal('show').find('.modal-body').html('Carregando...').load(url, function() {
        });

        return false;
     });

     $(document).on('submit', '.form-ajax', function() {
         var $this = $(this);

         $.post($this.attr('action'), $this.serialize(), function(data) {
             console.log(data);
         });
         return false;
     });

    $('.btn-toggle-status').css('width', '95px');
    $('.btn-toggle-status').click(function() {
        var $this = $(this);

        if ($this.hasClass('done')) {
            $this.removeClass('done');
            $this.addClass('iddle');
            $this.text($this.data('iddle-text'));

            $this.removeClass('btn-success');
        } else {
            $this.addClass('done');
            $this.removeClass('iddle');

            $this.addClass('btn-danger');
            $this.text($this.data('done-text'));
        }
    });
    $('.btn-toggle-status').hover(function() {
        var $this = $(this);

        if ($this.hasClass('iddle')) {
            // $this.text('');
        } else {
            $this.text($this.data('transition-text'));
            $this.addClass('btn-success');
            $this.removeClass('btn-danger');
        }
    }, function() {
        var $this = $(this);
        if ($this.hasClass('iddle')) {
        } else {
            $this.text($this.data('done-text'));
            $this.removeClass('btn-success');
            $this.addClass('btn-danger');
        }
    });

    $('.btn-post-ajax').click(function() {
        var $this = $(this);
        var url = $this.data('url');
        var delay = 1000;
        var postData = $this.data('post-data');
        var urlRedirect = $this.data('url-redirect');
        var showSuccessMessage = $this.data('show-success-message');

        postData = (postData) ? $.parseJSON(postData.replace(/'/gi, "\"")) : {};

        swal({
            type: 'question',
            title: 'Confirmação',
            text: $this.data('confirm-pergunta'),
            showCancelButton: true,
            cancelButtonText: 'Cancelar'
        }).then(function() {
            swal({
                title: '',
                text: $this.data('loading-text')
            });
            swal.showLoading();

            window.setTimeout(function() {
                $.post(url, postData, function(a) {
                    if (showSuccessMessage) {
                        swal({
                            type: 'success',
                            title: 'Sucesso',
                            text: a.message,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    }
                    window.setTimeout(function() {
                        window.location = urlRedirect;
                    }, 1500);
                })
                .fail(function(a, b, c) {
                    swal({
                        type: 'error',
                        title: 'Erro',
                        text: a.responseJSON.message
                    });
                });
            }, delay);
        });

        return false;
    });

    atualizaToggleParent();

    function toggleParent($this) {
        console.log('Parent selector', $this.data('parent-selector'));
        console.log('Esconder selector:', $this.data('esconder-selector'));

        var $esconder = $this
            .parents($this.data('parent-selector'))
            .find($this.data('esconder-selector'));

        // console.log('Esconder: ', $esconder.html());

        if ($this.data('expandido') == 1) {
            var icon = $this.data('plus-icon');
            $this.data('expandido', 0);

            $esconder.stop().slideUp();
        } else {
            var icon = $this.data('minus-icon');
            $this.data('expandido', 1);

            $esconder.stop().slideDown();
        }
        $this.html('<span class="'+icon+'"></span>');
    }

});

$(document).on('click', '.toggle-all-panels', function() {
    var $this = $(this);
    var $panels = $this.parents('.panel').siblings('#carrega-conteudo').children('.panel');

    if ($this.data('expandido') == 1) {
        $this.find('span')
          .removeClass('fa-minus')
          .addClass('fa-plus');
    } else {
        $this.find('span')
          .addClass('fa-minus')
          .removeClass('fa-plus');
    }

    $this.data('expandido', !$this.data('expandido'));

    $panels.each(function() {
        var $selector = $(this).find('.toggle-parent');
        if ($selector.data('expandido') != $this.data('expandido')) {
            $selector.click();
        }
    });

});

function loading(text) {
    swal({
        text: text,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
}

function atualizaToggleParent() {

    console.log('Atualiza Toggle Parent');

    $('.toggle-parent').each(function() {
        var $this = $(this);

        var $esconder = $this
            .parents($this.data('parent-selector'))
            .find($this.data('esconder-selector'));

        if ($this.data('expandido') == 0) {
            var icon = $this.data('plus-icon');
            $esconder.stop().hide();
        } else {
            var icon = $this.data('minus-icon');
        }
        $this.html('<span class="'+icon+'"></span>');
    });

    $('.btn-save-resolvido').click(function() {

        var $this = $(this);
        var isChecked = $this.prop('checked');

        var novoStatusText = (isChecked) ? 'RESOLVIDO' : 'NÃO RESOLVIDO';

        swal({
            type: 'question',
            title: 'Alterar status crítico',
            html: 'Você reamente deseja alterar o status crítico da resposta para <strong>'+novoStatusText+'</strong>?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Alterar',
            showLoaderOnConfirm: true,
            preConfirm: function (email) {
                return new Promise(function (resolve, reject) {
                    var url = $this.data('url');
                    var data = {
                        _csrfToken: $('body').data('csrf-token'),
                        value: isChecked
                    };

                    $.post(url, data, function(data) {
                        resolve();
                    })
                    .fail(function(error) {
                        reject(error.responseJSON.message);
                    });
                });
            },
        }).then(function () {
        }, function() {
            $this.prop('checked', !$this.prop('checked'));
        });
    });

    $('.toggle-target-from-checkbox').click(function() {
        console.log('i');
        var target = $(this).data('target');
        if ($(this).prop('checked')) {
            $('.' + target).slideDown();
        } else {
            $('.' + target).slideUp();
        }
    });
    
    $('.cod-visita').mask('aaa-9999');

}
