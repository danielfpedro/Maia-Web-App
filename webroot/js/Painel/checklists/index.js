$(function() {

    var getJsonRequest;
    var getJsonRequestPerguntas;
    var postImportar;

    $('.btn-inativar-visitas').click(function() {
        var $this = $(this);
        var url = $this.attr('href');

        swal({
            type: 'question',
            title: 'Inativar Visitas',
            html: 'Você realmente deseja inativar <strong>TODAS AS VISITAS</strong> desta checklist?',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            preConfirm: function() {
                return new Promise(function (resolve, reject) {
                    $.getJSON(url, function(data) {
                        resolve(data);
                    })
                    .fail(function() {
                        reject();
                    });
                });
            }
        }).then(function(data) {
            var message = "Esta checklist não possuía <strong>NENHUMA VISITA ATIVA</strong> então nada mudou."
            if (data.totalAfetadas == 1) {
                message = "<strong>" + data.totalAfetadas + "</strong> visita foi inativada.";
            } else if(data.totalAfetadas > 1) {
                message = "<strong>" + data.totalAfetadas + "</strong> visitas foram inativadas.";
            }

            swal({
                type: 'success',
                title: 'Resultado',
                html: message
            });
        }, function() {
            console.log('Cancelou Confirm');
        });

        console.log('URL', url);
        return false;
    });

    function limpaModalImportar() {
        $('.modal-importar-carrega-perguntas').html('');
        $('#modelos').val('');

        if (typeof getJsonRequest != 'undefined') {
            getJsonRequest.abort();
        }
        if (typeof getJsonRequestPerguntas != 'undefined') {
            getJsonRequestPerguntas.abort();
        }
        if (typeof postImportar != 'undefined') {
            postImportar.abort();
        }
    }

    $('#modelos').change(function() {

        $('.btn-importar-modelo').attr('disabled', true);

        if (typeof getJsonRequestPerguntas != 'undefined') {
            getJsonRequestPerguntas.abort();
        }

        var $selectModelos = $(this);
        var url = $selectModelos.data('url-carrega-perguntas');
        var $carregaPerguntas = $('.modal-importar-carrega-perguntas');

        $carregaPerguntas.html('');

        console.log('Antes', url);
        url = url.replace('{{checklistId}}', $selectModelos.val());
        console.log('Depois', url);

        if ($(this).val()) {

            $('.importar-modelo-loader').show();

            getJsonRequestPerguntas = $.getJSON(url, function(checklistComPerguntas) {
                $('.btn-modal-importar-modelo-fechar').attr('disabled', false);
                $('.btn-importar-modelo').attr('disabled', false);

                console.log('Checklist com perguntas', checklistComPerguntas);

                if (1 == 1) {

                    $('#nome-do-questionario').val(checklistComPerguntas.nome);

                    var $nomeQuestionario = $('<input/>')
                        .attr('name', 'nome_questionario')
                        .attr('required', true)
                        .attr('placeholder', 'Nome do Questionário')
                        .val(checklistComPerguntas.nome)
                        .addClass('form-control')
                        .appendTo($carregaPerguntas)
                        .focus();

                    var $textHelp = $('<p/>')
                        .addClass('help-block')
                        .text('Se você quiser pode alterar o nome do Questionário.')
                        .insertAfter($nomeQuestionario);

                    var $ulChecklist = $('<ul/>')
                        .addClass('list-unstyled')
                        .css('margin-top', '30px')
                        .appendTo($carregaPerguntas);

                    // var $li = $('<li/>')
                    //     .hide()
                    //     .html('<div class="checkbox"><input type="checkbox" class="modal-checklist-checkbox-toggle" id="modal-checklist-checkbox-'+checklistComPerguntas.id+'" checked><label for="modal-checklist-checkbox-'+checklistComPerguntas.id+'" style="font-weight: bold; font-size: 15px;">'+checklistComPerguntas.nome+'</label></div>')
                    //     .appendTo($ulChecklist)
                    //     .fadeIn('fast');

                    $.each(checklistComPerguntas.perguntas_por_setores, function(index, setor) {
                        var $li = $('<li/>').appendTo($ulChecklist);
                        var $ulSetores = $('<ul/>')
                            .addClass('list-unstyled')
                            .css({'margin-left': '0'})
                            .appendTo($li);
                        var $li = $('<li/>')
                            .hide()
                            .html('<div class="checkbox"><input type="checkbox" class="modal-checkbox-toggle" id="modal-setor'+parseInt(setor.id)+'" name="setor[]" value="'+setor.id+'" checked><label for="modal-setor'+parseInt(setor.id)+'" style="font-weight: bold; font-size: 15px;">'+setor.nome+'</label></div>')
                            .appendTo($ulSetores)
                            .fadeIn('fast');

                        var $li = $('<li/>').appendTo($ulSetores);
                        var $ulPerguntas = $('<ul/>')
                            .addClass('list-unstyled')
                            .css({'margin-left': '28px'})
                            .appendTo($li);
                        $.each(setor.perguntas, function(i, pergunta) {

                            var $liPergunta = $('<li/>')
                                .html('<div class="checkbox"><input type="checkbox" checked name="importar_perguntas[]" id="pergunta-'+pergunta.id+'" value="'+pergunta.id+'" class="modal-checkbox-pergunta"><label for="pergunta-'+pergunta.id+'">'+pergunta.pergunta+'</label></div>')
                                .appendTo($ulPerguntas);
                        });
                    });
                }

                $selectModelos.attr('disabled', false);
                $('.importar-modelo-loader').hide();

                $('.btn-importar-modelo').attr('disabled', false);
            })
            .fail(function(error) {
                console.log('Error do carregando perguntas', error);
                if (error.readyState != 0) {
                    swal({
                        type: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao carregar as perguntas do Questionário. Por favor, tenten novamente.'
                    }).then(function() {
                        $('.importar-modelo-loader').hide();
                        $('#modal-importar-do-modelo').modal('hide');
                    });
                }
            });
        }

    });



    $('.open-modal-importar-modelo').click(function() {

        var $selectModelos = $('#modelos');

        $('.btn-importar-modelo').attr('disabled', true);

        $selectModelos
            .html('')
            .append("<option value=\"\">Carregando, aguarde...</option>")
            .attr('disabled', true);

        getJsonRequest = $.getJSON($selectModelos.data('url-carrega-modelos'), function(modelos) {

            console.log('Modelos', modelos);

            if (modelos.length > 0) {
                $selectModelos
                    .html('')
                    .append("<option value=\"\">Selecione o Quesitonário:</option>");

                $.each(modelos, function(key, modelo) {
                    $selectModelos.append("<option value=\""+modelo.id+"\">"+modelo.nome+"</option>");
                });

                $selectModelos.attr('disabled', false);
            } else {
                swal({
                    type: 'info',
                    title: 'Nenhum Modelo',
                    text: 'No momento não temos nenhum modelo de questionário para o segmento da sua rede.'
                }).then(function() {
                    $('#modal-importar-do-modelo').modal('hide');
                });
            }
        })
        .fail(function(error) {
            // console.log('error', error);
            if (error.readyState != 0) {
                swal({
                    type: 'error',
                    title: 'Erro',
                    text: 'Ocorreu um erro ao carregar os modelos. Por favor, tenten novamente.'
                }).then(function() {
                    $('#modal-importar-do-modelo').modal('hide');
                });
            }
        });
    });

    $('#modal-importar-do-modelo').on('click', 'input.modal-checkbox-toggle', function() {
        var $this = $(this);
        var newValue = $this.prop('checked');
        console.log('New Value', newValue);
        $this.parents('li').find('li > ul input').prop('checked', newValue);
    });

    $('#modal-importar-do-modelo').on('click', 'input.modal-checklist-checkbox-toggle', function() {
        var $this = $(this);
        var newValue = $this.prop('checked');
        console.log('New Value', newValue);
        $('.modal-checkbox-toggle, .modal-checkbox-pergunta').prop('checked', newValue);
    });

    $('#modal-importar-do-modelo').on('hidden.bs.modal', function () {
        limpaModalImportar();
    })

    $('#modal-importar-form').submit(function() {
        var $this = $(this);
        var url = $(this).attr('action');
        var data = $(this).serialize();

        url = url.replace('{{checklistId}}', $('#modelos').val());

        $this.attr('disabled', true);
        $('.btn-importar-modelo')
            .html("<span class=\"fa fa-spinner fa-spin\"></span> Importando")
            .attr('disabled', true);

        postImportar = $.post(url + '.json', data, function(res) {
            console.log('Resposta submit form', res);

            window.location.reload();
        })
        .fail(function(error) {
            var text = error.responseJSON.message;
            swal({
                type: 'error',
                title: 'Erro',
                html: text
            });

            console.log('Error tal', error.responseJSON.code);
            $this.attr('disabled', false);
            $('.btn-importar-modelo').attr('disabled', false);
        })
        .always(function() {
            $('.btn-importar-modelo')
                .html("<span class=\"fa fa-check\"></span> Importar");
        });

        return false;
    });

});
