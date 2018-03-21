$(function() {

    var totalVisitasEncerradas = parseInt($('#total-visitas-encerradas').val());

    $('#fileupload')
        .fileupload({
            dataType: 'json',
            autoUpload: true,
            start: function() {
                // throw 'Error2';
                return false;
            },
            submit: function(e, data) {
                data.formData = {
                    '_csrfToken': $('#csrf-token').val(),
                    // 'pergunta_id': currentPergunta.id
                };


            },
            add: function(e, data) {
                data.url = $('#url-upload-imagem').val();

                var uid = guid();

                var $tr = $('<tr/>')
                    .addClass('')
                    .attr('id', 'imagem-linha-' + uid)
                    .html('<td colspan="3"><span class="label-carregamento"><span class="fa fa-arrow-circle-up"></span> Enviando imagem...</span><div class="progress progress-small progress-striped active" id="" style="margin-top: 15px;"><div class="progress-bar" role="progressbar"style="width: 0%;"></div></div></td>');

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
                    console.log('NOME ARQUIVO', data.result.imagem.nome_arquivo);
                    console.log('FOLDER', data.result.imagem.folder);

                    $tr.fadeOut(function() {
                        $tr.html('');

                        $bloco = $('#bloco-imagem-checklist').clone();
                        $bloco = $($bloco.html());

                        var baseUrl = $('#static-files-base').val();

                        $bloco.find('.bloco-imagem-a')
                            .attr({
                                'href': baseUrl + data.result.imagem.folder + data.result.imagem.nome_arquivo,
                                'data-lightbox': 'imagem',
                                'data-title': ''
                            });
                        $bloco.find('.bloco-imagem-imagem').attr('src', baseUrl + data.result.imagem.folder + 'quadrada_' + data.result.imagem.nome_arquivo);
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

    $('#fileupload').change(function() {
        //console.log('Seus falsos');
        //return false;
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
        // Desabilata os botões enquanto não carrega as perguntas
        $('.btn-ordenar-setores').attr('disabled', true);
        $('.btn-open-modal-pergunta').attr('disabled', true);
        $('.btn-importar-perguntas').attr('disabled', true);

        console.log('Carregando Perguntas do Banco');
        $.getJSON($('#url-carrega-perguntas').val(), function(data) {

            dados = data.perguntasPorSetorOrdenado;
            console.log('Perguntas do banco', dados);

            // Esconde o loader
            $('.perguntas-loader').fadeOut(function() {

                // Habilita os botões enquanto não carrega as perguntas
                //
                $('.btn-ordenar-setores').attr('disabled', false);
                if (!$('.btn-open-modal-pergunta').data('nao-reabilita')) {
                    $('.btn-open-modal-pergunta').attr('disabled', false);
                }
                if (!$('.btn-importar-perguntas').data('nao-reabilita')) {
                    $('.btn-importar-perguntas').attr('disabled', false);
                }

                // if (data.perguntasPorSetorOrdenado.length == 0) {
                //     $('.alert-nenhuma-pergunta').fadeIn();
                // }

                // $.each(data.perguntasPorSetorOrdenado, function(key, setor) {
                //     $.each(setor.perguntas, function(k, pergunta) {
                //         // console.log('Adicionando Pergunta.', pergunta);
                //         addPerguntaToDados(pergunta);
                //     });
                // });
                montaSetoresESuasPerguntas();

                atualizaToggleParent();

                if (dados.length < 1) {
                  $('#msg-sem-perguntas').fadeIn();
                }

                $('.panel-checklist-total-perguntas').text(getTotalPerguntasText(getTotalPerguntas()));
            });

            console.log('DADOS', dados);
        })
        .fail(function() {
            mensagemErroEncerrarExecucao('Ocorreu um erro ao carregar as perguntas.');
        });
    }

    function getTotalPerguntasText (total) {
      if (total == 1) {
          return '('+ total + ' pergunta)';
      }

      return '(' + total + ' perguntas)';
    }

    function getTotalPerguntas() {
      var out = 0;

      $.each(dados, function(index, setor) {
        out += setor.perguntas.length;
      });

      return out;
    }

    $(document).on('click', '.toggle-parent', function() {
        console.log('Toggle Parent');
        var setor = $(this).parents('.panel-setor').data('setor');
        setor.expandido = $(this).data('expandido');
        $(this).data('setor', setor);

        var setorIndex = getSetorIndex(setor.id);

        dados[setorIndex] = setor;

        console.log('Setor expandido', setor);
    });

    function montaSetoresESuasPerguntas() {
        console.log('Montando tudo', dados);
        var $carregaConteudo = $('#carrega-conteudo');

        $carregaConteudo.html('');

        var perguntaCounter = 1;
        $.each(dados, function(index, setor) {
          var $panelSetor = montaSetor(setor, $carregaConteudo);
          // Agora carrego as perguntas dentro do painel do setor
          $.each(setor.perguntas, function(index, pergunta) {
              console.log('Pergunta', pergunta);
              var $panelPergunta = montaPergunta(pergunta, $panelSetor, perguntaCounter);

              if (pergunta.tipo == 1) {
                  $panelPergunta.find('.panel-pergunta-msg-sem-alternativas').hide();
                  $.each(pergunta.alternativas, function(i, alternativa) {
                      montaAlternativa(alternativa, $panelPergunta);
                  });
              } else {
                  $panelPergunta.find('.list-group').hide();
                  $panelPergunta.find('.panel-pergunta-msg-sem-alternativas').show();
              }
              perguntaCounter++;
          });

          console.log('Setor', setor);
        });

        console.log('Final do monta, dados', dados);
    }

    function montaAlternativa(alternativa, $panelPergunta) {
        var $listaAlternativa = $panelPergunta.find('.panel-pergunta-li-alternativa-proto').clone();
        $listaAlternativa
          .removeClass('panel-pergunta-li-alternativa-proto');

        $listaAlternativa.find('.panel-pergunta-li-alternativa-alternativa').text(alternativa.alternativa);
        $listaAlternativa.find('.panel-pergunta-li-alternativa-valor').text((alternativa.valor != null) ? alternativa.valor : '-');

        if (alternativa.tem_foto) {
            $listaAlternativa.find('.panel-pergunta-li-alternativa-foto').html('<span class="fa fa-check color10"></span>');
        } else {
            $listaAlternativa.find('.panel-pergunta-li-alternativa-foto').html('<span class="fa fa-minus color3"></span>');
        }

        if (alternativa.item_critico) {
            $listaAlternativa.find('.panel-pergunta-li-alternativa-item-critico').html('<span class="fa fa-check color10"></span>');
        } else {
            $listaAlternativa.find('.panel-pergunta-li-alternativa-item-critico').html('<span class="fa fa-minus color3"></span>');
        }

        $panelPergunta.find('.list-group').append($listaAlternativa.fadeIn());

        return $listaAlternativa;
    }

    function montaSetor(setor, $carregaConteudo) {
        console.log('Montando setor', setor);
        // Pego o prototipo do painel do setor
        var $panelSetor = $('.panel-setor-proto').clone();
        $panelSetor.removeClass('panel-setor-proto');
        // Insiro o nome do setor no prototipo já clonado
        $panelSetor.find('.panel-setor-text-title').text(setor.nome);
        $panelSetor.find('.panel-setor-text-total-perguntas').text(setor.perguntas.length);

        $panelSetor.data('setor', setor);
        $panelSetor.addClass('panel-setor');

        $carregaConteudo.append($panelSetor.fadeIn());

        if (setor.expandido == 1) {
            console.log('Expandir!', $panelSetor.find('.toggle-parent'));
            $btn = $panelSetor.find('.toggle-parent');
            $btn.data('expandido', 1);
            $btn.find('span').removeClass('fa-plus').addClass('fa-minus')
            $panelSetor.find('.panel-setor-carrega-conteudo').show();
        }

        return $panelSetor;
    }

    function montaPergunta(pergunta, $panelSetor, counter) {
        // Pego o prototipo do painel da pergunta
        var $panelPergunta = $('.panel-pergunta-proto').clone();
        $panelPergunta.data('pergunta', pergunta);
        $panelPergunta.removeClass('panel-pergunta-proto');
        // Insiro o nome do setor no prototipo já clonado
        $panelPergunta.find('.panel-pergunta-text-counter').text('# ' + counter);
        $panelPergunta.find('.panel-pergunta-text-pergunta').text(pergunta.pergunta);
        $panelPergunta.find('.panel-pergunta-text-total-imagens').text(pergunta.imagens.length);
        if (totalVisitasEncerradas > 0) {
            $panelPergunta.find('.btn-remover-pergunta').hide();
            $panelPergunta.find('.btn-mover-pergunta').hide();
        }

        $panelSetor.find('.panel-setor-carrega-conteudo').append($panelPergunta.fadeIn());

        return $panelPergunta;
    }
    function montaAlternativas(pergunta, $panelSetor, counter) {
        // Pego o prototipo do painel da pergunta
        var $panelPergunta = $('.panel-pergunta-proto').clone();
        $panelPergunta.removeClass('panel-pergunta-proto');
        // Insiro o nome do setor no prototipo já clonado
        $panelPergunta.find('.panel-pergunta-text-counter').text('# ' + counter);
        $panelPergunta.find('.panel-pergunta-text-pergunta').text(pergunta.pergunta);
        $panelSetor.append($panelPergunta);

        return $PanelPergunta;
    }

    function addLinhaImagem(imagem) {
        $bloco = $('#bloco-imagem-checklist').clone();
        $bloco = $($bloco.html());

        var baseUrl = $('#static-files-base').val();

        $tr = $('<tr/>');

        $bloco.find('.bloco-imagem-a')
            .attr({
                'href': baseUrl + imagem.folder + imagem.nome_arquivo,
                'data-lightbox': 'imagem',
                'data-title': ''
            });
        $bloco.find('.bloco-imagem-imagem').attr('src', baseUrl + imagem.folder + 'quadrada_' + imagem.nome_arquivo);
        $bloco.find('textarea').val(imagem.legenda);
        $bloco.appendTo($tr).show();

        $tr.data('imagem', imagem);
        $('#container-carrega-imagens > table > tbody').append($tr);
    }

    function getPerguntaDataFromForm() {
        // Recebe ID por parametro pq o id é criado no server side e retorna
        var pergunta = {
            id: $('#id').val(),
            setor_id: parseInt($('#setores').val()),
            pergunta: $('#pergunta').val(),
            tipo: $('#questao-tipo').val(),
            alternativas: [],
        };

        $('.linha-opcoes').each(function() {
            var $this = $(this);
            pergunta.alternativas.push({
                id: $this.find('.id').val(),
                alternativa: $this.find('.opcao').val(),
                valor: $this.find('.valor').val(),
                ordem: $this.find('.ordem').val(),
                tem_foto: ($this.find('.tem-foto').is(':checked')) ? true : false,
                item_critico: ($this.find('.item-critico').is(':checked')) ? true : false
            });
        });

        pergunta.imagens = getImagensData();

        return pergunta;
    }

    function addPerguntaToDados(pergunta) {
        console.log('Add Pergunta to Dados', pergunta);
        var setorIndex = getSetorIndex($('#setores').val());

        console.log('Setor Index', setorIndex);

        if (setorIndex < 0) {
            var setorIndex = criaSetor({
                id: $('#setores').val(),
                nome: $('#setores option:selected').text()
            });
        }

        var perguntaNoDados = getPerguntaAndIndexById(pergunta.id);

        console.log('Pergunta no Dados', perguntaNoDados);

        if (perguntaNoDados.perguntaIndex < 0) {
            console.log('Pergunta não existia previamente no DADOS vou pushar', perguntaNoDados);
            dados[setorIndex].perguntas.push(pergunta);
        } else {
            if (perguntaNoDados.pergunta.setor_id != pergunta.setor_id) {
                console.log('Pergunta foi para um setor diferente');
                console.log('Deleto antiga e pusho no novo setor');
                dados[perguntaNoDados.setorIndex].perguntas.splice(perguntaNoDados.perguntaIndex, 1);
                console.log('Depois do splice', dados[perguntaNoDados.setorIndex].perguntas);

                dados[setorIndex].perguntas.push(pergunta);

                // Se o setor não tiver sobrado nenhuma pergunta eu deleto ele...
                // importante fazer o push antes pois já pegamos o index do setor novo
                // entao se chamassemos depois do splice mudariam os index e teriamos que checar novamente
                if (dados[perguntaNoDados.setorIndex].perguntas.length < 1) {
                    dados.splice(perguntaNoDados.setorIndex, 1);
                }
            } else {
                console.log('Pergunta mesmo setor... só insiro no index dela mesmo');
                dados[perguntaNoDados.setorIndex].perguntas[perguntaNoDados.perguntaIndex] = pergunta;
            }
        }
        console.log('Dados lenth', dados);
        if (dados.length > 0) {
            console.log('Dados lenth', dados);
            $('#msg-sem-perguntas').fadeOut();
        }
        $('.panel-checklist-total-perguntas').text(getTotalPerguntasText(getTotalPerguntas()));
    }

    function criaSetor(setor) {
        setor.perguntas = [];
        dados.push(setor);

        // A index do setor será o tamanho novo menos 1 pois ele é inserido no final do array, exemplo:
        // tenho o tamanho de 2 logo a index será... 0: primeiro, 1: segundo(<- Nosso)
        // 2 - 1 = 1 oq é a index correta do novo setor.
        return dados.length - 1;
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
                        removerPergunta(pergunta);
                        montaSetoresESuasPerguntas();
                        showAlertPop('A Pergunta foi deletada.', 'success', 3500);
                        resolve();
                    })
                    .fail(function(error) {
                        showAlertPop(error.responseJSON.message, 'error', 4000);
                        swal.closeModal();
                        reject();
                    });
                });
            }
        }).then(function() {

        }, function() {
          swal.closeModal();
        });

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
            limpaAlternativas();

            $.each(alternativas, function(index, alternativa) {
                console.log('Alternativa para carregar Antes', alternativa)
                if (typeof alternativa.item_critico == 'string') {
                    alternativa.item_critico = Boolean(parseInt(alternativa.item_critico));
                }
                if (typeof alternativa.tem_foto == 'string') {
                    alternativa.tem_foto = Boolean(parseInt(alternativa.tem_foto));
                }
                console.log('Alternativa para carregar', alternativa)
                addOpcao(alternativa, false);
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
        $('#carrega-conteudo').sortable("cancel");
        endOrdenarGrupos();
    });

    $('.btn-salvar-ordenacao-setores').click(function() {
        var $this = $(this);

        $this.attr('disabled', true);
        $('.btn-cancelar-ordenacao-setores').attr('disabled', true);

        var currentHtml = $this.html();
        $this.html('<span class="fa fa-spinner fa-spin"></span> Salvando');

        var setoresOrdenarIds = [];
        $('.panel-setor').each(function() {
            console.log('Setor', $(this).data('setor'));
            var setor = $(this).data('setor');
            setoresOrdenarIds.push(setor.id);
        });

        console.log('Setores Ordenar', setoresOrdenarIds);
        $.post($('#url-reordenar-setores').val(), {setores: setoresOrdenarIds, _csrfToken: csrfToken}, function(data) {
            console.log(data);
            endOrdenarGrupos();
        })
        .fail(function(error) {
            showAlertPop(error.responseJSON.message, 'error', 4000);
        })
        .always(function() {
            $this.html(currentHtml);
            $this.attr('disabled', false);
            $('.btn-cancelar-ordenacao-setores').attr('disabled', false);
        });
        //
        // window.setTimeout(function() {
        //     endOrdenarGrupos();
        //     $this.html(currentHtml);
        // }, 2000)
    });

    function startOrdenarGrupos(){
        $('.panel-setor').each(function() {
            var $this = $(this);

            $this.css('z-index', 9999);

            var $btn = $this.find('.toggle-parent');

            console.log('Valor do expandido', $btn.data('expandido'));
            $btn.data('expandido-antes-ordenar', $btn.data('expandido'));

            if ($btn.data('expandido') == 1) {
                $btn.click();
            }

            $btn.fadeOut('fast');

            $('.panel-buttons-ordenar-setores').css('z-index', 9999).fadeIn();
            // if (expandido) {
            //     $this.find('h4 > span').removeClass('fa-plus-square');
            //     $this.find('h4 > span').addClass('fa-minus-square');
            // } else {
            //     $this.find('h4 > span').removeClass('fa-minus-square');
            //     $this.find('h4 > span').addClass('fa-plus-square');
            // }
            //
            // $this
            //     .animate({'padding': '10px 20px'}, 400)
            //     .addClass('ordenando-setor');
            //
            // $this
            //     .find('.container-perguntas')
            //     .slideUp('normal', function() {
            //     });
        });

        // $('.tools-ordenar-setores').find('.btn').hide();
        // $('.tools-ordenar-setores').slideDown(function() {
        //     $('.tools-ordenar-setores').find('.btn').fadeIn();
        // });
        //
        // $('.btn-ordenar-setores').attr('disabled', true);
        // $('.btn-open-modal-pergunta').attr('disabled', true);
        //
        // $('.btn-salvar-ordenacao-setores').attr('disabled', false);
        // $('.btn-cancelar-ordenacao-setores').attr('disabled', false);

        $('#carrega-conteudo').sortable({
            axis: 'y',
            opacity: 0.8,
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

        $('.panel-buttons-ordenar-setores').fadeOut();

        $('.carrega-perguntas').sortable('destroy');

        $('.panel-setor').each(function() {
            var $this = $(this);
            var $btn = $this.find('.toggle-parent');;

            console.log('Expandido', $btn.data('expandido'));
            console.log('expandido-antes-ordenar', $btn.data('expandido-antes-ordenar'));
            if ($btn.data('expandido-antes-ordenar') == 1) {
                $btn.click();
            }
            $btn.fadeIn();

            $this.css('z-index', 1);
        });

        toggleOverlay();

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
            currentPerguntaMover = $(this).parents('.pergunta').data('pergunta');
            // console.log('Current pergunta mover', currentPerguntaMover);
            console.log('DADOS!', dados);
            criaSelectPerguntas(currentPerguntaMover);
            $('#modal-mover-pergunta').modal();
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
        },
        update: function() {
            atualizaOpcoesOrdem();
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
        // addPerguntaToDados(pergunta);
    }

    $(document).on('click', '.btn-edita-pergunta', function() {

        var $this = $(this);
        var $panel = $this.parents('.panel')
        var pergunta = $panel.data('pergunta')

        console.log('Pergunta a ser editada', pergunta);

        currentPergunta = pergunta;

        limpaForm();

        $('.modal-pergunta-text-total-imagens').text(pergunta.imagens.length);

        console.log('Current Pergunta', currentPergunta);
        $.each(pergunta.imagens, function(index, imagem) {
            addLinhaImagem(imagem);
        });

        var readonly = (totalVisitasEncerradas > 0) ? true: false;

        $('#id').val(pergunta.id);
        $('#pergunta').val(pergunta.pergunta)
            .attr('readonly', readonly);

        $('#questao-tipo').val(pergunta.tipo);

        $('#setores').val(pergunta.setor_id);
        // $('#my-modal').find('.modal-title').text('Editar Pergunta "'+truncText(pergunta.pergunta, 30)+'"');

        $('#modelo-alternativas').val('');

        $('.container-opcoes > .form-group > table > tbody').html('');

        if (parseInt(pergunta.tipo) == 1) {
            $.each(pergunta.alternativas, function(index, opcao) {
                addOpcao(opcao, true)
            });
            $('.wrap-container-opcoes').slideDown();

            $('#modelo-alternativas').parents('.form-group').show();
        } else {
            $('.wrap-container-opcoes').slideUp();

            $('#modelo-alternativas').parents('.form-group').hide();
        }

        if ($this.data('type') == 'imagens') {
            $('a[href="#imagens-tab"]').tab('show');
        } else {
            $('a[href="#pergunta-tab"]').tab('show');
        }

        // No final pois independe dos outros
        if (readonly) {
            $('#modelo-alternativas').parents('.form-group').hide();
            $('#setores').parents('.form-group').hide();
            $('#questao-tipo').parents('.form-group').hide();

            $('.modal-pergunta-setor-static-form-group').show();
            $('.modal-pergunta-setor-static').text(pergunta.setor.nome);

            $('.add-opcao').hide();

            $('.modal-pergunta-alert-ordenar').hide();

            if ($('.container-opcoes > .form-group > table > tbody').data('ui-sortable')) {
                $('.container-opcoes > .form-group > table > tbody')
                    .sortable('destroy');
            }
            $('.container-opcoes > .form-group > table > tbody > tr').css('cursor', 'default');
        }
    });

    $('button.btn-open-modal-pergunta').click(function() {
        var $this = $(this);
        var $panel = $this.parents('.panel')
        var pergunta = $panel.data('pergunta')

        limpaForm();

        // Muda o título da -modal pq quando editando ela é diferente
        //$('#my-modal').find('.modal-title').text('Adicionar Pergunta');

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

        var fechar = false;

        if (!validaForm()) {
            $('a[href="#pergunta-tab"]').tab('show');
            return false;
        }
        if ($('#questao-tipo').val() == 1) {
            if (!validaOpcoes()) {
                $('a[href="#pergunta-tab"]').tab('show');
                return false;
            }
        }

        $('#my-modal').find('input, button, select, textarea').attr('disabled', true);
        toggleBtnSalvarLoading($this, true);

        var pergunta = getPerguntaDataFromForm();

        if (pergunta.id) {
            fechar = true;
        }

        console.log('Pergunta para salvar', pergunta);
        //console.log('IMAGENS!', getImagensData());

        var url = (!pergunta.id) ? $('#url-add-pergunta').val() :  $('#url-edit-pergunta').val().replace('{{perguntaId}}', pergunta.id);

        $.post(url, {_csrfToken: csrfToken, pergunta}, function(data){
            pergunta.id = data.pergunta.id;
            addPerguntaToDados(pergunta);

            $('#my-modal').find('input, button, select, textarea').attr('disabled', false);
            toggleBtnSalvarLoading($this, false);

            $('#alert-success-bottom-right').fadeIn();
            window.setTimeout(function() {
                $('#alert-success-bottom-right').fadeOut();
            }, 3500);

            console.log('Fechar?', fechar);
            if (fechar) {
                $('#my-modal').modal('hide');
            }
            montaSetoresESuasPerguntas();

            limpaForm();
        })
        .fail(function(error) {

            $('#my-modal').find('input, button, select, textarea').attr('disabled', false);
            toggleBtnSalvarLoading($this, false);

            console.log('Error', error.responseJSON);
            showAlertPop(error.responseJSON.message, 'error', 4000);
        })
        .always(function() {
            window.setTimeout(function() {
                $('#pergunta').focus();
            }, 500);
            $('a[href="#pergunta-tab"]').tab('show');
        });
    });

    function showAlertPop(error, type, duration) {
        $('#alert-'+type+'-bottom-right')
            .stop()
            .hide()
            .fadeIn()
            .find('p')
            .text(error);
        window.setTimeout(function() {
            $('#alert-error-bottom-right').fadeOut();
        }, 3500);
    }

    function getImagensData() {
        var $imagens = $('#container-carrega-imagens > table > tbody > tr').not('#bloco-imagem-checklist');

        var imagens = [];
        var i = 0;
        $imagens.each(function() {
            console.log('Aqui na iteração');
            var imagem = $(this).data('imagem');
            console.log('Imagem', imagem);
            if (typeof imagem != 'undefined') {
                imagens.push(imagem);
            }
            i++;
        });

        return imagens;
    }

    function toggleBtnSalvarLoading($this, flag){
        if (flag) {
            $this.data('default-html', $this.html());
            $this.html('<span class="fa fa-spinner fa-spin"></span> Salvando');
        } else {
            $this.html($this.data('default-html'));
        }
        $this.attr('disabled', flag);
    }

    $('.btn-salvar-mover').click(function() {
        var $this = $(this);

        console.log('Dados btn mover', dados);

        $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', true);

        var currentHtml = $this.html();
        $this.html('<span class="fa fa-spinner fa-spin"></span> Movendo');

        // É a pergunta para aonde vamos mover para antes ou depois
        var perguntaReferencia = getPerguntaAndIndexById($('#perguntas-ordenar').val());
        var perguntaMover = getPerguntaAndIndexById(currentPerguntaMover.id);
        
        console.log('Pergunta referencia', perguntaReferencia);
        console.log('Pergunta mover', perguntaMover);

        // Insiro depois
        // O Setor pode mudar ou não, insiro o novo setor e danese se mudou ou não
        perguntaMover.pergunta.setor_id = perguntaReferencia.pergunta.setor_id;
        var passo = ($('#perguntas-ordenar-posicao').val() == 1) ? (perguntaReferencia.perguntaIndex + 1) : perguntaReferencia.perguntaIndex;

        // console.log('Dados', dados);
        // console.log('Pergunta Referencia', perguntaReferencia);

        dados[perguntaReferencia.setorIndex].perguntas.splice(passo, 0, perguntaMover.pergunta);

        // Deleto pergunta movida para deletar aonde ele estava antes
        dados[perguntaMover.setorIndex].perguntas.splice(perguntaMover.perguntaIndex, 1);
        if (dados[perguntaMover.setorIndex].perguntas.length < 1) {
            dados.splice(perguntaMover.setorIndex, 1);
        }


        // Pergunta que vamos mover para depois ou antes

        // console.log('Dados', dados);
        // montaSetoresESuasPerguntas();
        // $('#modal-mover-pergunta').modal('hide');
        // $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', false);
        // $this.html(currentHtml);

        var perguntasDoSetor = getPerguntasDoSetor(perguntaMover.pergunta.setor_id, ['id', 'setor_id', 'pergunta']);

        $.post($('#url-reordenar-perguntas').val(), {perguntas: perguntasDoSetor, _csrfToken: csrfToken}, function(data) {
            $('#modal-mover-pergunta').modal('hide');
            montaSetoresESuasPerguntas();
        })
        .fail(function(error) {
            showAlertPop(error.responseJSON.message, 'error', 4000);
        })
        .always(function(data) {
            $('#modal-mover-pergunta').find('input, button, select, textarea').attr('disabled', false);
            $this.html(currentHtml);
        });
    });

    function getSetorIndex(id) {
        var out = -1;
        $.each(dados, function(index, setor) {
            if (setor.id == id) {
                out = index;
                return false;
            }
        });
        return out;
    }

    function limpaForm() {
        $('#id').val('');
        $('#pergunta').val('');

        $('.container-opcoes > .form-group > table > tbody').html('');
        if ($('#modelo-alternativas').val()) {
            $('#modelo-alternativas').change();
        } else if ($('#questao-tipo').val() == 1) {
            addOpcao(null, false);
            addOpcao(null, false);
        }

        if ($('#questao-tipo').val() == 2) {
            $('#modelo-alternativas').parents('.form-group').hide();
        } else {
            $('#modelo-alternativas').parents('.form-group').show();
        }

        $('.has-error').removeClass('has-error');

        limpaImagens();
    }

    function validaForm(){
        var $pergunta = $('#pergunta');
        if (!$pergunta.val()) {
            $pergunta.parents('.form-group').addClass('has-error');
            window.setTimeout(function() {
                $pergunta.focus();
            }, 200);
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
            // if (!$valor.val()) {
            //     $valor.focus();
            //     $valor.parent('.form-group').addClass('has-error');
            //     out = false;
            //     return false;
            // } else {
            //     $valor.parent('.form-group').removeClass('has-error');
            // }
        });

        return out;
    }

    function addOpcao(opcao, goFocus) {
        var opcaoVazia = {
            id: '',
            alternativa: '',
            valor: '',
            tem_foto: false,
            item_critico: false
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


        console.log('Opção Item Critico', opcao);

        var readonly = (totalVisitasEncerradas > 0) ? true: false;

        var $alternativa = $('<td><div class="form-group"><input type="hidden" value="'+opcao.id+'" class="id"><input type="text" class="form-control opcao" id="opcao-'+index+'" value="'+opcao.alternativa+'" '+((readonly) ? 'readonly' : '')+' placeholder=""></div></td>')
            .appendTo($tr);

        console.log('Total opções', getTotalOpcoes());

        var $txtOrdem = $('<input type="hidden" class="ordem" id="ordem-'+index+'">')
            .appendTo($alternativa);

        var $pontuacao = $('<td><div class="form-group"><input type="number" class="form-control text-center valor" id="valor-'+index+'" '+((readonly) ? 'readonly' : '')+' value="'+opcao.valor+'" placeholder=""></div></td>')
            .appendTo($tr);
        var $foto = $('<td class="text-center"><div class="checkbox checkbox-primary has-tooltip"><input type="checkbox" class="tem-foto" id="tem-foto-'+index+'" ' + ((Boolean(opcao.tem_foto)) ? 'checked' : '') + '><label for="tem-foto-'+index+'"></label></div></td>')
            .appendTo($tr);
        var $opcaoCritica = $('<td class="text-center"><div class="checkbox checkbox-primary has-tooltip"><input type="checkbox" class="item-critico" id="item-critico-'+index+'" ' + ((Boolean(opcao.item_critico)) ? 'checked' : '') + '><label for="item-critico-'+index+'"></label></div></td>')
            .appendTo($tr);

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

        if (total <= 1 || readonly) {
            $('.btn-remove-opcao').attr('disabled', true);
        } else {
            $('.btn-remove-opcao').attr('disabled', false);
        }

        atualizaOpcoesOrdem();
    }

    $('#questao-tipo').change(function() {

        console.log('Changeando questao tipo!');
        $('.wrap-container-opcoes').slideToggle();

        if ($('#questao-tipo').val() == 1) {
            var total = getTotalOpcoes();
            if (total < 1) {
                addOpcao(null, true);
                addOpcao(null, false);
            } else if (total == 1) {
                addOpcao(null, false);
            }

            $('#modelo-alternativas').parents('.form-group').fadeIn('fast');
        } else {
            $('#modelo-alternativas').parents('.form-group').fadeOut('fast');
        }
    });

    function atualizaOpcoesOrdem() {
        var i = 1;
        console.log('Loop nas opções');
        $('.container-opcoes > .form-group > table > tbody > tr').each(function() {
            console.log('Dentro do loop');
            $(this).find('input.ordem').val(i);
            i++;
        });
    }

    function getTotalOpcoes() {
        return $('.container-opcoes > .form-group > table > tbody > tr').length;
    }

    function removeOpcao($linha) {
        var total = getTotalOpcoes();
        if (total <= 2) {
            return false;
        }
        $linha.fadeOut('fast', function() {
            $(this).remove();
            // Se tem tres e ele acabou de deletar uma quer dizer que
            // eu devo desabilitar todos os botoes
            if (total <= 3) {
                $('.btn-remove-opcao').attr('disabled', true);
            }

            atualizaOpcoesOrdem();
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

    function limpaImagens() {
        $('#container-carrega-imagens > table > tbody > tr').not('#bloco-imagem-checklist').remove();

        $('.modal-pergunta-text-total-imagens').text(0);
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
        console.log('Tey', dados);
        $('#perguntas-ordenar')
            .html('')
            .append('<option value="">Carregando opções, aguarde...</option>')
            .attr('disabled', true);

        $('#perguntas-ordenar').html('');

        var i = 1;
        var setoresParaAdd = [];

        $.each(dados, function(index, setor) {
            var setorVal = {
                id: setor.id,
                nome: setor.nome,
                perguntas: []
            }

            var perguntas = [];

            $.each(setor.perguntas, function(indexPergunta, pergunta) {
                var perguntaVal = pergunta;
                if (perguntaVal.id != perguntaParaMover.id) {
                    perguntaVal.number = i;
                    perguntas.push(perguntaVal);
                }
                i++;
            });

            if (perguntas.length > 0) {
                setorVal.perguntas = perguntas;
                setoresParaAdd.push(setorVal);
            }

        });

        $.each(setoresParaAdd, function(idx, setor) {
            var html = '<optgroup label="'+setor.nome+'">';
            $.each(setor.perguntas, function(indexPergunta, pergunta) {
                html += '<option value="'+pergunta.id+'">'+ pergunta.number + ') ' + truncText(pergunta.pergunta, 50)+'</option>';
            });

            html += '</optgroup>';
            $('#perguntas-ordenar').append(html);

        });

        $('#perguntas-ordenar').attr('disabled', false);
    }

    function removerPergunta(pergunta){
        var dadosPergunta = getPerguntaAndIndexById(pergunta.id);

        dados[dadosPergunta.setorIndex].perguntas.splice(dadosPergunta.perguntaIndex, 1);

        if (dados[dadosPergunta.setorIndex].perguntas.length < 1) {
            dados.splice(dadosPergunta.setorIndex, 1);
        }

        if (dados.length < 1) {
            $('#msg-sem-perguntas').fadeIn();
        }
        $('.panel-checklist-total-perguntas').text(getTotalPerguntasText(getTotalPerguntas()));
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
            i += setor.perguntas.length;
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


    function getPerguntaAndIndexById(perguntaId) {
        var out = {
            setorIndex: -1,
            perguntaIndex: -1,
            pergunta: {}
        };
        var achou = false;
        $.each(dados, function(indexSetor, setor) {
            $.each(setor.perguntas, function(indexPergunta, pergunta) {
                if (pergunta.id == perguntaId) {
                    out = {
                        setorIndex: indexSetor,
                        perguntaIndex: indexPergunta,
                        pergunta: pergunta
                    };
                    return;
                }
            });
            if (achou) {
                return;
            }
        });

        return out;
    }

    function getPerguntasDoSetor(setorId, fields) {

        var perguntasTemp = [];
        var perguntas = [];

        $.each(dados, function(index, setor) {
            if (setor.id == setorId) {
                perguntasTemp = setor.perguntas;
                return;
            }
        });

        if (perguntasTemp.length > 0 && typeof fields != undefined && fields.length > 0) {
            $.each(perguntasTemp, function(idx, pergunta) {
                var perguntaParaAdd = {};

                $.each(fields, function(i, field) {
                    perguntaParaAdd[field] = perguntasTemp[idx][field];
                });

                perguntas.push(perguntaParaAdd);
            });
        }

        return perguntas;
    }

});
