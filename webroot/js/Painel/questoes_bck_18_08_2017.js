$(function() {

    $('#fileupload')
        .fileupload({
            dataType: 'json',
            autoUpload: true,
            submit: function(e, data) {
                data.formData = {
                    '_csrfToken': $('#csrf-token').val(),
                    'pergunta_id': currentPergunta.id
                };
            },
            add: function(e, data) {
                data.url = $('#url-upload-imagem').val().replace('{{perguntaId}}', currentPergunta.id);

                var uid = guid();

                var $tr = $('<tr/>')
                    .addClass('')
                    .attr('id', 'imagem-linha-' + uid)
                    .html('<td colspan="3"><span class="label-carregamento"><span class="fa fa-arrow-circle-o-up"></span> Enviando imagem...</span><div class="progress progress-small progress-striped active" id="" style="margin-top: 15px;"><div class="progress-bar" role="progressbar"style="width: 0%;"></div></div></td>');

                $('#container-carrega-imagens > table > tbody').append($tr);

                data.uid = uid;

                data.submit();

                $('.btn-salvar-imagens').attr('disabled', true);
            },
            progress: function (e, data) {
                console.log('Progress');
                var progress = parseInt(data.loaded / data.total * 100, 10);
                if (progress >= 90) {
                    progress = 90;
                }
                $('#imagem-linha-'+data.uid+' > td > .progress > .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            fail: function(e, data) {
                console.log('Resultado do error!', data);
                var $tr = $('#imagem-linha-' + data.uid);

                $('#imagem-linha-'+data.uid+' > td > .progress > .progress-bar').css(
                    'width',
                    '100%'
                );
                window.setTimeout(function() {
                    $tr
                        .find('td')
                        .addClass('color10 text-center')
                        .html('<span class="fa fa-frown-o"></span> Ocorreu um erro ao tentar fazer o upload da sua imagem.');
                }, 800);
            },
            done: function(e, data) {
                console.log('Resultado do done!', data.result);

                var $tr = $('#imagem-linha-' + data.uid);

                $('#imagem-linha-'+data.uid+' > td > .progress > .progress-bar').css(
                    'width',
                    '100%'
                );

                window.setTimeout(function() {
                    $tr.fadeOut(function() {
                        $tr.html('');

                        $bloco = $('#bloco-imagem-checklist').clone();
                        $bloco = $($bloco.html());

                        var baseUrl = $('#url-webroot').val();

                        $bloco.find('.bloco-imagem-a')
                            .attr({
                                'href': baseUrl + data.result.imagem.full_image_path,
                                'data-lightbox': 'imagem',
                                'data-title': ''
                            });
                        $bloco.find('.bloco-imagem-imagem').attr('src', baseUrl + data.result.imagem.full_image_quadrada_path);
                        $bloco.appendTo($tr).show();

                        $tr.data('imagem', data.result.imagem);

                        $tr.fadeIn();
                    });
                }, 800);
            },
            always: function() {
                window.setTimeout(function() {
                    $('.btn-salvar-imagens').attr('disabled', false);
                }, 800);
            }
        });

    /**
     * Dados da pagina
     * @type {Object}
     */
    var dados = [];
    var currentPergunta = null;
    var currentPerguntaMover = null;

    var csrfToken = $('#csrf-token').val();

    carregaPerguntasDoBanco();

    function carregaPerguntasDoBanco() {
        $('.btn-ordenar-setores').attr('disabled', true);
        $('.btn-open-modal-pergunta').attr('disabled', true);

        $.getJSON($('#url-carrega-perguntas').val(), function(data) {

            dados = data.perguntasPorSetorOrdenado;
            console.log('Perguntas do banco', dados);

            $('.perguntas-loader').fadeOut(function() {

                $('.btn-ordenar-setores').attr('disabled', false);
                $('.btn-open-modal-pergunta').attr('disabled', false);

                if (data.perguntasPorSetorOrdenado.length == 0) {
                    $('.alert-nenhuma-pergunta').fadeIn();
                }

                $.each(data.perguntasPorSetorOrdenado, function(key, setor) {
                    $.each(setor.perguntas, function(k, pergunta) {
                        // console.log('Adicionando Pergunta.', pergunta);
                        addPergunta(pergunta);
                    });
                });

                refreshAll();
            });
        })
        .fail(function() {
            mensagemErroEncerrarExecucao('Ocorreu um erro ao carregar as perguntas.');
        });
    }

    function addLinhaImagem(imagem) {
        $bloco = $('#bloco-imagem-checklist').clone();
        $bloco = $($bloco.html());

        var baseUrl = $('#url-webroot').val();

        $tr = $('<tr/>');

        $bloco.find('.bloco-imagem-a')
            .attr({
                'href': baseUrl + imagem.full_image_path,
                'data-lightbox': 'imagem',
                'data-title': ''
            });
        $bloco.find('.bloco-imagem-imagem').attr('src', baseUrl + imagem.full_image_quadrada_path);
        $bloco.find('textarea').val(imagem.legenda);
        $bloco.appendTo($tr).show();

        $tr.data('imagem', imagem);
        $('#container-carrega-imagens > table > tbody').append($tr);
    }

    /**
     * Adiciona a pergunta
     * @param {object} pergunta Pergunta a ser adicionada
     */
    function addPergunta(pergunta) {

        $panel = $('<div/>')
            .data('pergunta', pergunta)
            .attr('id', 'painel-pergunta-' + pergunta.id)
            .addClass('panel panel-default pergunta')
            .hide();

        $panelHeading = $('<div/>')
            .addClass('panel-title')
            .html('<span class="pergunta-counter">-</span>) ' + pergunta.pergunta+'<ul class="panel-tools"><li><button type="button" class="btn-mover-pergunta icon" title="Mover" data-toggle="tooltip"><i class="fa fa-arrows-v fa-fw"></i></button></li><li><button type="button" class="btn-abrir-modal-imagens icon" data-toggle="modal" data-target="#modal-imagens"><i class="fa fa-picture-o fa-fw"></i> <small class="imagens-counter"></small></button></li><li><button type="button" class="icon expand-tool btn-edita-pergunta has-tootlip" data-toggle="modal" data-target="#my-modal" title="Editar"><i class="fa fa-pencil fa-fw"></i></button></li><li><button type="button" class="btn-remover-pergunta icon closed-tool" data-toggle="tooltip" title="Deletar"><i class="fa fa-times fa-fw"></i></button></li></ul>')
            .appendTo($panel);

        $panelBody = $('<div/>')
            .addClass('panel-body')
            .appendTo($panel);

        $panelList = $('<ul/>')
            .addClass('list-group')
            .appendTo($panelBody);

        if (pergunta.tipo == 2) {
            $panelBody
                .addClass('dissertativa')
                .html('<p><em class="text-muted">Perguntas dissertativas não possuem alternativas.</em></p>')
        } else {
            $panelList.css({'margin-top': '-5px'});
        }

        if (pergunta.tipo == 1) {
            $panelListLi = $('<li class="list-group-item"><div class="row"><div class="col-md-5" style="font-weight: bold;">Alternativa</div><div class="col-md-3 text-center" style="font-weight: bold;">Valor</div><div class="col-md-2 text-center"><span class="fa fa-camera"></span></div><div class="col-md-2 text-center"><span class="fa fa-exclamation-triangle"></span></div></div></li>')
                .appendTo($panelList);

            $.each(pergunta.alternativas, function(index, opcao) {
                $panelListLi = $('<li class="list-group-item"><div class="row"><div class="col-md-5">'+opcao.alternativa+'</div><div class="col-md-3 text-center">'+opcao.valor+'</div><div class="col-md-2 text-center">'+((opcao.tem_foto) ? '<span class="fa fa-check color10"></span>' : '<span class="fa fa-minus color3"></span>') +'</div><div class="col-md-2 text-center">'+((opcao.item_critico) ? '<span class="fa fa-check color10"></span>' : '<span class="fa fa-minus color3"></span>') +'</div></div></li>')
                    .appendTo($panelList);
            });
        }

        /**
         * Verifico se existe o container do setor da pergunta caso não crio
         * caso contrario somente faço o append no existente.
         */
        var $setor = $('#setor-' + pergunta.setor.id);
        // console.log('SETOR', $setor.length);
        // console.log('Setor length', $setor.length);
        if ($setor.length == 0) {
            $setor = $('<div/>')
                .attr('id', 'setor-'+pergunta.setor.id)
                .data('id', pergunta.setor.id)
                .data('expandido', 1)
                .addClass('setor');

            $setor.append('<h4 class="toggle-perguntas">'+ '<span class="fa fa-minus-square"></span> ' + pergunta.setor.nome + '<small class="setor-perguntas-counter"></small></h4>')
            $('.carrega-perguntas').append($setor);
            var $container = $('<div/>').addClass('container-perguntas');
            $setor.append($container);
            // console.log('SETOR', $setor);
        }

        $container = $setor.find('.container-perguntas');

        /**
         * CurrentPergunta carrega a pergunta que está na modal aberta, nesse caso
         * para edição, a gente insere depois e remove o painel antigo
         */
        if (currentPergunta) {
            var $perguntaAntiga = $('#painel-pergunta-' + currentPergunta.id);

            console.log('Pergunta Antiga', $perguntaAntiga);
            console.log('Pergunta Atual', $panel);

            $panel.insertAfter($perguntaAntiga);
            if (pergunta.setor_id != currentPergunta.setor_id) {
                $perguntaAntiga.slideDown(function() {
                    $(this).remove();
                });
                $container.append($panel)
                $panel.slideDown();
            } else {
                $perguntaAntiga.slideUp(function() {
                    $panel.slideDown();
                    $(this).remove();
                });
            }
        } else {
            $container.append($panel)
            $panel.slideDown();
        }

        $('.alert-nenhuma-pergunta').fadeOut('fast');
    }

    /**
     * Remove a pergunta
     */
    $(document).on('click', '.btn-remover-pergunta', function() {
        var $this = $(this);

        swal({
            type: 'question',
            title: 'Remover Pergunta',
            text: 'Você realmente deseja remover esta pergunta?',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            cancelButtonText: 'Cancelar',
            preConfirm: function() {
                return new Promise(function (resolve, reject) {
                    /**
                     * Pego o ID da pergunta para enviar e deletar a pergunta
                     */
                    var $painelPergunta = $this.parents('.pergunta');
                    var pergunta = $painelPergunta.data('pergunta');

                    var url = $('#url-remover-pergunta').val().replace('{{perguntaId}}', pergunta.id);
                    console.log('URL', url);

                    $.post(url, {_csrfToken: csrfToken}, function() {
                        removerPergunta($painelPergunta);
                        resolve();
                    })
                    .fail(function() {
                        mensagemErroEncerrarExecucao('Ocorreu um erro ao tentar deletar a sua pergunta.');
                        reject();
                    });
                });
            }
        }).then(function() {

        }, function() {});

        return false;
    });

    // Helpers
    //
    /**
     * Em uma chamada ajax se ocorreu um erro que não era para aconceter e vc quiser encerrar a execução
     * da página vai aparecer esse alert com um botão de atualizar a página
     */
    function mensagemErroEncerrarExecucao(message) {
        swal({
            type: 'error',
            title: 'Erro',
            text: message,
            allowEscapeKey: false,
            allowOutsideClick: false,
            confirmButtonText: '<span class="fa fa-repeat"></span> Atualizar Página',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function (resolve, reject) {
                    location.reload();
                });
            }
        })
        .then(function() {

        }, function() {

        });
    }


    $(document).on('click', '.btn-checklist-remove-imagem', function() {
        $this = $(this);
        // $this.find('span').addClass('fa-spin fa-spinner');
        $tr = $this.parents('tr');
        // $tr.css({'opacity': .5, 'pointer-events': 'none'});

        // var imagem = $tr.data('imagem');

        // console.log('Imagem', imagem);

        $tr.fadeOut(function() {
            $(this).remove();
        });

        // $.post($('#url-deleta-imagem').val() + '/' + currentPergunta.id + '/imagens/delete/' + imagem.id + '.json', {_csrfToken: csrfToken}, function() {
        //     $tr.fadeOut(function() {
        //         $(this).remove();
        //         refreshAll();
        //     })
        // });
    });
    $(document).on('keyup', '.checklist-legenda-imagem', function() {
        $this = $(this);
        var valor = $this.val();
        var $linha = $this.parents('tr');
        $this.parent('td').prev('td').find('a').attr('data-title', valor);

        var data = $linha.data('imagem');
        // console.log('Data da linha', $linha.data());
        data.legenda = valor;
        $linha.data({'imagem': data});
    });
    $(document).on('mouseenter', '#bloco-imagem-checklist', function() {
        $(this).find('.container-overlay-imagem').stop().fadeIn('fast');
    });
    $(document).on('mouseleave', '#bloco-imagem-checklist', function() {
        $(this).find('.container-overlay-imagem').stop().fadeOut('fast');
    });

    // Colocando dados de teste
    // $('#modelo-alternativas > option').each(function() {
    //     var $this = $(this);
    //
    //     if ($this.val()) {
    //         var alternativas = $this.data('alternativas');
    //         console.log('Option', alternativas);
    //     }
    // });

    $('#modelo-alternativas').change(function() {
        var $this = $(this);

        if ($this.val()) {
            alternativas = $this.find('option:selected').data('alternativas');
            limpaAlternativas()

            $.each(alternativas, function(index, value) {
                addOpcao(value, false);
            });
        } else {
            limpaAlternativas()
            addOpcao(null, true);
            addOpcao(null, false);
        }
    });

    function limpaAlternativas() {
        $('.container-opcoes > .form-group > table > tbody').html('');
    }

    $('#my-modal').on('shown.bs.modal', function () {
        $('#pergunta').focus();
        // $('#pergunta').parents('.form-group').removeClass('has-error');
        // $('#pergunta').focus();
        // limpaForm();
        //
        // console.log('DADOS', dados);
    });

    $('#my-modal').on('hide.bs.modal', function () {
        // limpaForm();
    });

    $('.btn-ordenar-setores').click(function() {
        if (contaSetores() < 2) {
            swal({
                type: 'info',
                title: 'Ação não permitida',
                text: 'Você deve ter ao menos dois grupos para ordená-los.'
            })
        } else {
            startOrdenarGrupos();
        }
    });

    $('.btn-cancelar-ordenacao-setores').click(function() {
        $('.carrega-perguntas').sortable("cancel");
        endOrdenarGrupos();
    });

    $('.btn-salvar-ordenacao-setores').click(function() {
        var $this = $(this);

        $this.attr('disabled', true);
        $('.btn-cancelar-ordenacao-setores').attr('disabled', true);

        var currentHtml = $this.html();
        $this.html('<span class="fa fa-spinner fa-spin"></span> Salvando');

        var setoresOrdenar = [];
        $('.setor').each(function() {
            console.log('Setor', $(this).data());
            setoresOrdenar.push($(this).data('id'));
        });

        $.post($('#url-reordenar-setores').val(), {setores: setoresOrdenar, _csrfToken: $('#csrf-token').val()}, function(data) {
            console.log(data);
        });

        window.setTimeout(function() {
            endOrdenarGrupos();
            $this.html(currentHtml);
        }, 2000)
    });

    function startOrdenarGrupos(){
        $('.setor').each(function() {
            var $this = $(this);
            var expandido = $this.data('expandido');
            if (expandido) {
                $this.find('h4 > span').removeClass('fa-plus-square');
                $this.find('h4 > span').addClass('fa-minus-square');
            } else {
                $this.find('h4 > span').removeClass('fa-minus-square');
                $this.find('h4 > span').addClass('fa-plus-square');
            }

            $this
                .animate({'padding': '10px 20px'}, 400)
                .addClass('ordenando-setor');

            $this
                .find('.container-perguntas')
                .slideUp('normal', function() {
                });
        });

        $('.tools-ordenar-setores').find('.btn').hide();
        $('.tools-ordenar-setores').slideDown(function() {
            $('.tools-ordenar-setores').find('.btn').fadeIn();
        });

        $('.btn-ordenar-setores').attr('disabled', true);
        $('.btn-open-modal-pergunta').attr('disabled', true);

        $('.btn-salvar-ordenacao-setores').attr('disabled', false);
        $('.btn-cancelar-ordenacao-setores').attr('disabled', false);

        $('.carrega-perguntas').sortable({
            axis: 'y',
            opacity: 0.5,
            containment: 'parent',
            tolerance: "pointer",
            // handle: ".handle",
            cancel: 'input, button',
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
            }
        });

        toggleOverlay()
    }

    function endOrdenarGrupos(){

        $('.setor').each(function() {
            var $this = $(this);
            var $containerPerguntas = $this.find('.container-perguntas');

            var expandido = $this.data('expandido');


            $this
                .animate({'padding': 0}, 400)
                .removeClass('ordenando-setor');

            if (expandido) {
                $containerPerguntas.slideDown();
            } else {
                $containerPerguntas.slideUp();
            }

            $this.removeClass('ordenando-setor');
        });

        $('.tools-ordenar-setores').find('.btn').fadeOut(function() {
            $(this).hide();
            $('.tools-ordenar-setores').slideUp();
        });


        $('.btn-ordenar-setores').attr('disabled', false);
        $('.btn-open-modal-pergunta').attr('disabled', false);

        $('.carrega-perguntas').sortable('destroy');

        toggleOverlay();

        refreshAll();
    }

    function toggleOverlay() {
        var $overlay = $('.overlay');

        if ($overlay.length) {
            $overlay.fadeOut(function () {
                $(this).remove();
            });
        } else {
            $overlay = $('<div/>')
                .addClass('overlay');
            $overlay
                .hide()
                .appendTo('body')
                .fadeIn();
        }
    }

    function criaSelectSetores() {
        $('#setores-ordenar').html('');
        $.each(dados, function(index, value) {
            console.log(value);
            $('#setores-ordenar').append('<option value="">'+setores[index]+'</option>');
        });
    }

    $(document).on('click', '.btn-mover-pergunta', function(event) {
        if (contaPerguntas() < 2) {
            event.preventDefault();
            swal({
                type: 'info',
                title: 'Ação não permitida',
                text: 'Você deve ter ao menos duas perguntas para ordená-las.'
            });
        } else {
            $('#modal-mover-pergunta').modal();
            currentPerguntaMover = $(this).parents('.pergunta').data('pergunta');
            criaSelectPerguntas(currentPerguntaMover);
        }
    });

    $(document).on('click', '.toggle-perguntas', function() {
        var $this = $(this);
        var $setor = $this.parents('.setor');
        var expandido = $setor.data('expandido');

        if (expandido == 0) {
            $setor.data('expandido', 1);
            $this.find('span').removeClass('fa-plus-square');
            $this.find('span').addClass('fa-minus-square');
        } else {
            $setor.data('expandido', 0);
            $this.find('span').addClass('fa-plus-square');
            $this.find('span').removeClass('fa-minus-square');
        }

        $setor.find('.container-perguntas').stop().slideToggle();
    });

    $('.container-opcoes > .form-group > table > tbody').sortable({
        axis: 'y',
        opacity: 0.5,
        containment: 'parent',
        tolerance: "pointer",
        // handle: ".handle",
        cursor: 'pointer',
        cancel: 'input, button',
        start: function(e, ui){
            ui.placeholder.height(ui.item.height());
        }
    });

    $('#container-carrega-imagens > table > tbody').sortable({
        axis: 'y',
        opacity: 0.5,
        containment: 'parent',
        tolerance: "pointer",
        // handle: ".handle",
        cursor: 'pointer',
        cancel: 'input, button, textarea',
        start: function(e, ui){
            ui.placeholder.height(ui.item.height());
        }
    });

    $(document).on('mouseenter', '.pergunta', function() {
        // console.log($(this));
        // $opcoes = $('div.menu-opcoes');
        // $(this).append($opcoes);
        // $opcoes.show();
    });

    for (var i = 1; i < 0; i++) {
        var setorId = Object.keys(setores)[i-1];
        var setorNome = setores[i];
        var pergunta = {
            id: 'fdslkjfs' + i,
            pergunta: 'Qual o seu nomeasd aiosd ad asdu asiod aiodsaoudas oiudaosu diaus diaus diua douisa?',
            tipo: 1,
            perguntaObrigatoria: true,
            setor: {
                id: setorId,
                nome: setorNome
            },
            opcoes : [
                {
                    alternativa: 'Bom',
                    valor: 10,
                    tem_foto: false
                },
                {
                    alternativa: 'Bom',
                    valor: 10,
                    tem_foto: true
                },
                {
                    alternativa: 'Bom',
                    valor: 10,
                    tem_foto: false
                },
            ]
        }
        // if (typeof dados[setorId] == 'undefined') {
        //     dados[setorId] = {};
        // }
        // dados[setorId][pergunta.id] = pergunta;
        // addPergunta(pergunta);
    }

    $(document).on('click', '.btn-edita-pergunta', function() {

        var $this = $(this);
        var $panel = $this.parents('.panel')
        var pergunta = $panel.data('pergunta')

        currentPergunta = pergunta;

        $('#pergunta').val(pergunta.pergunta);
        $('#questao-tipo').val(pergunta.tipo);
        $('#setores').val(pergunta.setor.id);

        $('#my-modal').find('.modal-title').text('Editar Pergunta "'+truncText(pergunta.pergunta, 30)+'"');

        $('#modelo-alternativas').val('');

        $('.container-opcoes > .form-group > table > tbody').html('');

        if (parseInt(pergunta.tipo) == 1) {
            $.each(pergunta.alternativas, function(index, opcao) {
                addOpcao(opcao, true)
            });
            $('.wrap-container-opcoes').slideDown();
        } else {
            $('.wrap-container-opcoes').slideUp();
        }
    });

    $('button.btn-open-modal-pergunta').click(function() {
        var $this = $(this);
        limpaForm();
        // Muda o título da modal pq quando editando ela é diferente
        $('#my-modal').find('.modal-title').text('Adicionar Pergunta');

        if ($('#modelo-alternativas').val()) {
            $('#modelo-alternativas').change();
        }
    });

    $('.add-opcao').click(function() {
        addOpcao();
    });

    $(document).on('click', '.btn-remove-opcao', function() {
        removeOpcao($(this).parents('tr'));
    });

    $('.btn-toggle-checkbox-tem-foto').click(function() {
        $this = $(this);
        var checked = $this.data('checked');

        if (checked) {
            $('.tem-foto').prop('checked', false);
            $this.data('checked', 0);
        } else {
            $('.tem-foto').prop('checked', true);
            $this.data('checked', 1);
        }
    });

    $('.btn-toggle-checkbox-item-critico').click(function() {
        $this = $(this);
        var checked = $this.data('checked');

        if (checked) {
            $('.item-critico').prop('checked', false);
            $this.data('checked', 0);
        } else {
            $('.item-critico').prop('checked', true);
            $this.data('checked', 1);
        }
    });

    $('.btn-salvar-pergunta').click(function() {
        var $this = $(this);
        var fechar = $this.data('fechar');

        if (!validaForm()) {
            return false;
        }
        if ($('#questao-tipo').val() == 1) {
            if (!validaOpcoes()) {
                return false;
            }
        }

        var setorId = parseInt($('#setores').val());
        var perguntaId = (currentPergunta) ? currentPergunta.id : null;

        if (typeof dados[setorId] == 'undefined') {
            dados[setorId] = {};
        }

        var pergunta = {
            id: perguntaId,
            pergunta: $('#pergunta').val(),
            tipo: $('#questao-tipo').val(),
            alternativas: [],
            setor: {
                id: setorId,
                nome: $('#setores option:selected').text()
            }
        };

        $('.linha-opcoes').each(function() {
            var $this = $(this);
            pergunta.alternativas.push({
                id: $this.find('.id').val(),
                alternativa: $this.find('.opcao').val(),
                valor: $this.find('.valor').val(),
                tem_foto: ($this.find('.tem-foto').is(':checked')) ? 1 : 0,
                item_critico: ($this.find('.item-critico').is(':checked')) ? 1 : 0
            });
        });

        $('#my-modal').find('input, button, select, textarea').attr('disabled', true);

        toggleBtnSalvarLoading($this, true);

        var perguntaEnviar = pergunta;
        perguntaEnviar['_csrfToken'] = $('#csrf-token').val();
        console.log('Current Pergunta', pergunta);
        var url = (!pergunta.id) ? $('#url-add-pergunta').val() :  $('#url-edit-pergunta').val().replace('{{perguntaId}}', pergunta.id);
        $.post(url, perguntaEnviar, function(data){
            pergunta = data.pergunta

            addPergunta(pergunta);
            refreshAll();
            limpaForm();

            swal({
                type: 'success' ,
                title: 'Confirmado',
                text: 'A pergunta foi salva com sucesso',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(function() {

            }, function(dismiss) {
                if (dismiss === 'timer') {
                    $('#pergunta').focus();
                    if (currentPergunta || fechar == 1) {
                        $('#my-modal').modal('hide');
                    }
                }
            });
        })
        .fail(function(data) {
            swal({
                type: 'error',
                title: 'Erro',
                html: data.responseJSON.message
            });
        })
        .always(function() {
            $('#my-modal').find('input, button, select, textarea').attr('disabled', false);
            toggleBtnSalvarLoading($this, false);
        });
    });

    function toggleBtnSalvarLoading($this, flag){
        if (flag) {
            $this.data('default-html', $this.html());
            $this.html('<span class="fa fa-spinner fa-spin"></span> Salvando');
        } else {
            $this.html($this.data('default-html'));
        }
        $this.attr('disabled', flag);
    }

    $('.btn-salvar-imagens').click(function() {
        var $this = $(this);

        $('#modal-imagens').find('input, button, select, textarea').attr('disabled', true);

        var currentHtml = $this.html();
        $this.html('<span class="fa fa-spinner fa-spin"></span> Salvar');

        // Pergunta que vamos mover para depois ou antes
        // Exlcui o id do prototipo
        var $imagens = $('#container-carrega-imagens > table > tbody > tr').not('#bloco-imagem-checklist');

        var imagens = [];
        var i = 0;
        $imagens.each(function() {
            var imagem = $(this).data('imagem');
            // console.log('Imagem', imagem);
            if (typeof imagem != 'undefined') {
                imagens.push(imagem);
            }
            i++;
        });

        console.log('Salvando imagens', imagens);
        var url = $('#url-salva-imagens').val().replace('{{perguntaId}}', currentPergunta.id);

        $.post(url, {_csrfToken: csrfToken, imagens: imagens}, function() {

            currentPergunta.imagens = imagens;
            $('#painel-pergunta-' + currentPergunta.id).data('pergunta', currentPergunta);

            swal({
                type: 'success' ,
                title: 'Confirmado',
                text: 'As imagens foram salvas com sucesso',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(function() {

            }, function(dismiss) {
                if (dismiss === 'timer') {
                    $('#modal-imagens').modal('hide');
                }
            });
        })
        .always(function() {
            $('#modal-imagens').find('input, button, select, textarea').attr('disabled', false);
            $this.html(currentHtml);

            refreshAll();
        });

        return false;

        currentPerguntaMover.setor.id = perguntaReferencia.setor.id;

        if ($('#perguntas-ordenar-posicao').val() == 1) {
            $('#painel-pergunta-' + currentPerguntaMover.id).insertAfter($perguntaReferencia);
        } else {
            $('#painel-pergunta-' + currentPerguntaMover.id).insertBefore($perguntaReferencia);
        }

        refreshAll();
        console.log('Enviando para salvar o movimento', dados);

            var dadosEnviar = dados;
            dadosEnviar['_csrfToken'] = $('#csrf-token').val();

            $.post($('#url-reordenar-perguntas').val(), dadosEnviar, function(data) {

                $this.addClass('btn-success');
                $this.html('<span class="fa fa-check"></span> Movido com sucesso!');
            })
            .fail(function() {
                swal({
                    type: 'error',
                    title: 'Erro',
                    text: 'Ocorreu um erro ao tentar mover a sua pergunta.'
                });
                $this.addClass('btn-danger');
                $this.html('<span class="fa fa-ban"></span> Erro!');
            })
            .always(function(data) {
                $this.find('span').remove();

                window.setTimeout(function() {
                    $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', false);
                    $this.removeClass('btn-success');
                    $this.html(currentHtml);
                    if (data.status == 200) {
                        $('#modal-mover-pergunta').modal('hide');
                    }
                }, 1000);
            });
    });

    $('.btn-salvar-mover').click(function() {
        var $this = $(this);

        $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', true);

        var currentHtml = $this.html();
        $this.html('<span class="fa fa-spinner fa-spin"></span> Salvando');


        // Pergunta que vamos mover para depois ou antes
        var $perguntaReferencia = $('#painel-pergunta-' + $('#perguntas-ordenar').val());
        var perguntaReferencia = $perguntaReferencia.data('pergunta');

        currentPerguntaMover.setor_id = perguntaReferencia.setor.id;
        currentPerguntaMover.setor = perguntaReferencia.setor;

        if ($('#perguntas-ordenar-posicao').val() == 1) {
            $('#painel-pergunta-' + currentPerguntaMover.id).insertAfter($perguntaReferencia);
        } else {
            $('#painel-pergunta-' + currentPerguntaMover.id).insertBefore($perguntaReferencia);
        }

        refreshAll();
        console.log('Dados', dados);
        $.post($('#url-reordenar-perguntas').val(), {perguntas: extractFromPerguntas(['id', 'setor_id']), _csrfToken: csrfToken}, function(data) {
            $('#modal-mover-pergunta').modal('hide');
        })
        .fail(function() {
            mensagemErroEncerrarExecucao('Ocorreu um erro ao tentar reordenar as perguntas');
        })
        .always(function(data) {
            $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', false);
            $this.html(currentHtml);
        });
    });

    function extractFromPerguntas(campos) {
        var out = [];
        $.each(dados, function(index, setor) {
            $.each(setor.perguntas, function(idx, pergunta) {
                var obj = {};
                $.each(campos, function(ix, campo) {
                    obj[campo] = pergunta[campo];
                });
                out.push(obj);
            });
        });

        return out;
    }

    function getSetorIndex(id, array) {
        var out = null;
        $.each(array, function(index, setor) {
            if (setor.id == id) {
                out = index;
                return false;
            }
        });
        return out;
    }

    function limpaForm() {
        $('#pergunta').val('');

        $('.container-opcoes > .form-group > table > tbody').html('');
        if ($('#modelo-alternativas').val()) {
            $('#modelo-alternativas').change();
        } else if ($('#questao-tipo').val() == 1) {
            addOpcao(null, false);
            addOpcao(null, false);
        }

        $('.has-error').removeClass('has-error');
    }

    function validaForm(){
        var $pergunta = $('#pergunta');
        if (!$pergunta.val()) {
            $pergunta.parents('.form-group').addClass('has-error');
            $pergunta.focus();
            return false;
        } else {
            $pergunta.parents('.form-group').removeClass('has-error');
        }

        return true;
    }

    function validaOpcoes() {
        var out = true;
        $('.container-opcoes > .form-group > table > tbody > tr').each(function() {
            var $this = $(this);

            console.log('Dentro do each', $this);

            var $opcao = $this.find('.opcao');
            var $valor = $this.find('.valor');

            console.log('Opção', $opcao);

            if (!$opcao.val()) {
                $opcao.focus();
                $opcao.parent('.form-group').addClass('has-error');
                out = false;
                return false;
            } else {
                $opcao.parent('.form-group').removeClass('has-error');
            }
            if (!$valor.val()) {
                $valor.focus();
                $valor.parent('.form-group').addClass('has-error');
                out = false;
                return false;
            } else {
                $valor.parent('.form-group').removeClass('has-error');
            }
        });

        return out;
    }

    function refreshAll() {

        var i = 1;
        $('.pergunta-counter').text('-');

        var novo = [];
        $('.pergunta').each(function() {
            var $this = $(this);
            $this.find('.pergunta-counter').text(i);

            i++;

            var pergunta = $this.data('pergunta');
            console.log('Pergunta', pergunta);

            // Imagem
            $this.find('.btn-abrir-modal-imagens > .imagens-counter').text('('+pergunta.imagens.length+')');
            var setorIndex = getSetorIndex(pergunta.setor_id, novo);
            if (setorIndex == null) {
                console.log('Caiu no criar setor', setorIndex);
                var setor = pergunta.setor;
                setor.perguntas = [];
                setor.perguntas.push(pergunta);
                novo.push(setor);
            } else {
                console.log('Caiu no setor já criado com index', setorIndex);
                novo[setorIndex].perguntas.push(pergunta);
            }
        });
        dados = novo;
        console.log('Dados Novos', dados);

        // Deletando setores que ficaram vazios
        $('.setor').each(function() {
            var $this = $(this);
            var setorId = $this.data('id');
            if (!setorExiste(setorId)) {
                $this.fadeOut(function() {
                    if (dados.length < 1) {
                        $('.alert-nenhuma-pergunta').fadeIn();
                    }
                });
            } else {
                $this.find('.setor-perguntas-counter').text(' ('+contaPerguntasNoSetor(setorId)+')');
            }
        });
    }


    function setorExiste(setorId) {
        var index = getSetorIndex(setorId, dados);
        if (typeof dados[index] != 'undefined') {
            return true;
        }
        return false;
    }

    function addOpcao(opcao, goFocus) {
        var opcaoVazia = {
            id: '',
            alternativa: '',
            valor: '',
            tem_foto: 0,
            item_critico: 1
        };

        opcao = (typeof opcao == 'undefined' || !opcao) ? opcaoVazia : opcao;
        goFocus = (typeof goFocus == 'undefined') ? true : goFocus;

        var total = getTotalOpcoes();
        var index = total + 1;

        var $tr = $('<tr/>')
            .css({
                'cursor': 'ns-resize',
            })
            .addClass('linha-opcoes');


        var $alternativa = $('<td><div class="form-group"><input type="hidden" value="'+opcao.id+'" class="id"><input type="text" class="form-control opcao" id="opcao-'+index+'" value="'+opcao.alternativa+'" placeholder=""></div></td>')
            .appendTo($tr);
        var $pontuacao = $('<td><div class="form-group"><input type="number" class="form-control text-center valor" id="valor-'+index+'" value="'+opcao.valor+'" placeholder=""></div></td>')
            .appendTo($tr);
        var $foto = $('<td class="text-center"><div class="checkbox checkbox-primary has-tooltip"><input type="checkbox" class="tem-foto" id="tem-foto-'+index+'" ' + ((parseInt(opcao.tem_foto)) ? 'checked' : '') + '><label for="tem-foto-'+index+'"></label></div></td>')
            .appendTo($tr);
        var $opcaoCritica = $('<td class="text-center"><div class="checkbox checkbox-primary has-tooltip"><input type="checkbox" class="item-critico" id="item-critico-'+index+'" ' + ((parseInt(opcao.item_critico)) ? 'checked' : '') + '><label for="item-critico-'+index+'"></label></div></td>')
            .appendTo($tr);
        // var $obrigatorio = $('<td class="text-center"><div class="checkbox checkbox-primary"><input type="checkbox" name="ativo" value="1" id="ativoa"><label for="ativoa"></label></div></td>')
        //     .appendTo($tr);

        // var $btnResize = $('<td class="text-center" style="padding-top: 20px; padding-right: 5px"><button type="button" class="btn btn-light btn-xs btn-icon handle" title="Arraste para ordenar" data-toggle="tooltip"><span class="fa fa-s-v" style="cursor: pointer;"></span></button></td>')
        //     .appendTo($tr);

        var $btnRemove = $('<td class="text-center" style=""><button type="button" class="btn btn-light btn-xs btn-icon btn-remove-opcao" style="margin-top: 7px;"><span class="fa fa-times"></span></button></td>')
            .appendTo($tr);

        $('.container-opcoes').slideDown('fast');
        $('.alert-sem-perguntas').fadeOut('fast');

        // $tr.hide();
        $('.container-opcoes > .form-group > table > tbody').append($tr);
        $tr.fadeIn('normal', function() {
            if (goFocus) {
                $('#opcao-' + index).focus();
            }
        });

        if (total <= 1) {
            $('.btn-remove-opcao').attr('disabled', true);
        } else {
            $('.btn-remove-opcao').attr('disabled', false);
        }
    }

    $('#questao-tipo').change(function() {
        $('.wrap-container-opcoes').slideToggle();
        if ($('#questao-tipo').val() == 1) {
            var total = getTotalOpcoes();
            if (total < 1) {
                addOpcao(null, true);
                addOpcao(null, false);
            } else if (total == 1) {
                addOpcao(null, false);
            }

            $('#modelo-alternativas').parents('.form-group').fadeIn();
        } else {
            $('#modelo-alternativas').parents('.form-group').fadeOut();
        }
    });

    function getTotalOpcoes() {
        return $('.container-opcoes > .form-group > table > tbody > tr').length;
    }

    function removeOpcao($$linha) {
        var total = getTotalOpcoes();
        if (total <= 2) {
            return false;
        }
        $$linha.fadeOut('fast', function() {
            $(this).remove();
            // Se tem tres e ele acabou de deletar uma quer dizer que
            // eu devo desabilitar todos os botoes
            if (total <= 3) {
                $('.btn-remove-opcao').attr('disabled', true);
            }
        });
    }

    $('.btn-ordenar-perguntas').click(function() {
        if ($(this).data('estado') == 1) {
            startOrdenarPerguntasMode();
            $(this).data('estado', 2);
        } else {
            stopOrdenarPerguntasMode();
            $(this).data('estado', 1);
        }
    });


    $(document).on('click', '.btn-abrir-modal-imagens', function() {
        currentPergunta = $(this).parents('.pergunta').data('pergunta');
        limpaImagens();
        console.log('Current Pergunta', currentPergunta);
        $.each(currentPergunta.imagens, function(index, imagem) {
            addLinhaImagem(imagem);
        });
        // console.log('Current Pergunta', currentPerguntaModal);
    });

    function limpaImagens() {
        $('#container-carrega-imagens > table > tbody > tr').not('#bloco-imagem-checklist').remove();
    }

    function truncText(str, max, add){
       add = add || '...';
       return (typeof str === 'string' && str.length > max ? str.substring(0,max)+add : str);
    };

    /**
     * Cria opções do select para mover pergunta
     * @param  {object} pergunta passa a pergunta que está sendo movida para que ela não
     * aparecer já que ele não pode mover após ela mesmo, inclusive pela logia isso daria
     * problema
     */
    function criaSelectPerguntas(perguntaParaMover) {

        $('#perguntas-ordenar')
            .html('')
            .append('<option value="">Carregando opções, aguarde...</option>')
            .attr('disabled', true);

        $setores = $('div.setor');

        if ($setores.length > 0) {
            $('#perguntas-ordenar').html('');
            var i = 1;
            var contadorDaPergunta = 1;
            $setores.each(function() {
                var html = '<optgroup label="'+$(this).find('h4').text()+'">';
                    var $panels = $(this).find('.panel');
                    // Pergunta é um array pq eu comparo e se o array ficar vazio eu nem crio o setor
                    var perguntas = [];
                    $panels.each(function() {
                        var pergunta = $(this).data('pergunta');
                        if (pergunta.id != perguntaParaMover.id) {
                            pergunta.labelSelect = contadorDaPergunta + ') ' + pergunta.pergunta;
                            perguntas.push(pergunta);
                        }
                        contadorDaPergunta++;
                    });

                    $.each(perguntas, function(index, value) {
                        html += '<option value="'+value.id+'">'+truncText(value.labelSelect, 50)+'</option>';
                        i++;
                    });
                html += '</optgroup>';

                if (perguntas.length > 0) {
                    $('#perguntas-ordenar').append(html);
                }
            });
        }
        $('#perguntas-ordenar').attr('disabled', false);
    }

    function removerPergunta($painelPergunta){
        $painelPergunta.slideUp(function() {
            $(this).remove();
            refreshAll();
        });
    }

    function perguntaLoading($pergunta, flag) {
        if (flag == 'show') {
            $loading = $('<div class="progress progress-small progress-striped active pergunta-loading"><div class="progress-bar" role="progressbar" style="width: 100%;"></div></div>');
            $loading.hide();
            $pergunta
                // .find('.panel-body')
                .before($loading);

            $loading.fadeIn();
            $pergunta.fadeTo(1, 0.5);
        } else {
            $pergunta
                .find('.panel-body > .pergunta-loading')
                .slideUp(function() {
                    $(this).remove();
                });

            $pergunta.fadeTo('slow', 1);
        }
    }


    function contaPerguntas() {
        var i = 0;
        $.each(dados, function(index, setor) {
            var arrayKeys = Object.keys(setor);
            var total = arrayKeys.length;
            i = i + total;
        });
        return i;
    }

    function contaPerguntasNoSetor(setorId) {
        var index = getSetorIndex(setorId, dados);
        if (typeof dados[index] == 'undefined') {
            console.error('Setor não existe.');
            return false;
        } else {
            return dados[index].perguntas.length;
        }
        var i = 0;
    }

    function contaSetores() {
        return Object.keys(dados).length;
    }

    function guid() {
      function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
          .toString(16)
          .substring(1);
      }
      return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
        s4() + '-' + s4() + s4() + s4();
    }

    var autocompleteUrl = $('#autocomplete-checklists').data('url');
    $('#autocomplete-checklists').autocomplete({
        appendTo: '#modal-importar',
        source: autocompleteUrl,
        select: function(event, ui) {
            var $perguntasLista = $('#modal-importar-carrega-perguntas');
            $perguntasLista.find('li:not(.list-toggle)').remove();
            $perguntasLista.fadeIn('fast');

            if (ui.item.perguntas.length > 0) {
                var $ulChecklist = $('<ul/>')
                    .addClass('list-unstyled')
                    .appendTo($perguntasLista);

                var $li = $('<li/>')
                    .hide()
                    .html('<div class="checkbox"><input type="checkbox" class="modal-checklist-checkbox-toggle" id="modal-checklist-checkbox-'+ui.item.id+'" checked><label for="modal-checklist-checkbox-'+ui.item.id+'" style="font-weight: bold; font-size: 15px;">'+ui.item.label+'</label></div>')
                    .appendTo($ulChecklist)
                    .fadeIn('fast');

                $.each(ui.item.perguntas_por_setores_ordenados, function(index, setor) {
                    var $li = $('<li/>').appendTo($ulChecklist);
                    var $ulSetores = $('<ul/>')
                        .addClass('list-unstyled')
                        .css({'margin-left': '28px'})
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
        }
    });

    $('.modal').on('click', 'input.modal-checkbox-toggle', function() {
        var $this = $(this);
        var newValue = $this.prop('checked');
        console.log('New Value', newValue);
        $this.parents('li').find('li > ul input').prop('checked', newValue);
    });

    $('.modal').on('click', 'input.modal-checklist-checkbox-toggle', function() {
        var $this = $(this);
        var newValue = $this.prop('checked');
        console.log('New Value', newValue);
        $('.modal-checkbox-toggle, .modal-checkbox-pergunta').prop('checked', newValue);
    });

    $('#modal-importar-form').submit(function() {
        //var dadosForm = $(this).parents('#modal-importar').find('form');
        var $this = $(this);
        var url = $this.attr('action');

        toggleBtnSalvarLoading($('.btn-importar'), true);

        $.post(url, $this.serialize(), function() {
            location.reload();
        })
        .fail(function(error) {
            alert('Erro');
            toggleBtnSalvarLoading($('.btn-importar'), false);
        });
        return false;
    });

});
