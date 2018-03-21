$(function() {

    var gruposPorChecklistRequest = null;
    var urlEmails = $('#url-emails').val();
    var urlTodosOsGruposDeEmails = $('#url-todos-os-grupos-de-emails').val();

    $('.has-tagit').tagit();

    $('.emails-extra').tagit({
        placeholderText: 'Digite o Email...'
    });

    $('.check-loja').click(function() {
        console.log('Ao menos uma retornou', aomenosUmaLoja());
        $('.btn-salvar').attr('disabled', !aomenosUmaLoja());
    });

    function aomenosUmaLoja() {
        out = false;
        $('.check-loja').each(function() {
            if ($(this).prop('checked')) {
                out = true;
                return;
            }
        });

        return out;
    }

    $('#modelo-checklist-id').change(function() {

        var $this = $(this);

        if (gruposPorChecklistRequest) {
            gruposPorChecklistRequest.abort();
        }

        // atualizaSubmitState(true);
        if ($this.val()) {
            carregaEmails();
        }
    });

    // atualizaSubmitState(false);
    function atualizaSubmitState(transition) {
        if ($('#modelo-checklist-id').val()) {
            if (transition) {
                $('.container-lojas').slideDown();
            } else {
                $('.container-lojas').show();
            }
            
            $('.btn-salvar').attr('disabled', false);
        } else {
            $('.container-lojas').slideUp();
            if (transition) {
                $('.container-lojas').slideUp();
            } else {
                $('.container-lojas').hide();
            }
        }
    }
    
    if ($('#modelo-checklist-id').val()) {
        carregaEmails();
    }
    function carregaEmails() {
        $('.btn-salvar').attr('disabled', true);

        $('.container-loja').each(function() {
            $(this).find('.carrega-emails-resultados').html('<span class="fa fa-spinner fa-spin"></span> Carregando emails, aguarde...');
            $(this).find('.carrega-emails-criticos').html('<span class="fa fa-spinner fa-spin"></span> Carregando emails, aguarde...');
        });


        $('.grupos-de-emails')
            .attr('disabled', true)
            .val([])
            .trigger('change');

        gruposPorChecklistRequest = $.getJSON(urlEmails, {checklist_id: $('#modelo-checklist-id').val()}, function(grupos) {
            // console.log('Grupos', grupos);
            if (grupos.length > 0) {
                
                var selecionarGrupos = {};

                $.each(grupos, function(key, grupo) {
                    $('.container-loja').each(function() {
                        var lojaId = $(this).data('lojaId');

                        if (typeof selecionarGrupos[lojaId] == 'undefined') {
                            selecionarGrupos[lojaId] = [];
                        }

                        console.log('LojaID', lojaId);
                        var idsDasLojas = grupo.lojas.map(function(data) {
                            return data.id
                        });

                        // Se não tiver lojas não restringe nada, caso tenha eu vejo
                        if (grupo.lojas.length < 1 || idsDasLojas.indexOf($(this).data('lojaId')) > -1) {
                            selecionarGrupos[lojaId].push(grupo.id);
                        }
                    });
                });

                // Adiciono tudo que eu tinha gerado antes
                $('.container-loja').each(function() {
                    var lojaId = $(this).data('lojaId');
                    $(this).find('.grupos-de-emails').val(selecionarGrupos[lojaId])
                        .trigger('change');
                });
            }
        }).always(function() {
            $('.grupos-de-emails')
                .attr('disabled', false)
                .trigger('change');

            $('.btn-salvar').attr('disabled', !aomenosUmaLoja());
        });
    }

    $('body').on('click', '.btn-remove-grupo-emails', function() {
        /**$(this).parentsUntil('.row').fadeOut(function(){
            $(this).remove();
        });**/
    });

    function addGrupoDeEmailsAsLojas(grupo) {
        $('.container-lojas').each(function() {
            var $this = $(this);

        });
    }

    function addBlocoDeEmails($this, nome, containerName, emails) {
        var $emails = getGrupoDeEmailsClone();
        $emails.find('label').text(nome);
        var $input = $emails.find('input');
        $input.tagit({readOnly: true});

        $.each(emails, function(key, email) {
            console.log('Adicionando email', email);
            $input.tagit('createTag', email);
        });

        console.log('Adicionando log para ',  $this.find('.carrega-emails ' + containerName));
        $this.find('.carrega-emails ' + containerName).append($emails);
    }

    function getGrupoDeEmailsClone() {
        $out = $('#grupos-de-emails-prototipo').clone().attr('id', null);
        // Não dei um chain no show pq taa dando pau
        $out.show();
        return $out;
    }

    var checklistComSetores = $.parseJSON($('#checklists-setores').val());

    $('.date').datepicker('option', 'minDate', 0).datepicker('refresh');

    // Pego o json usuario e suas respectivas lojas
    var usuariosLojas = $.parseJSON($('#usuarios-lojas').val());
    console.log('Usuario slojas', usuariosLojas);
    $('#modelo-usuario-id option').each(function() {
        var $this = $(this);
        $.each(usuariosLojas, function(i, usuario) {
            if (parseInt($this.attr('value')) == usuario.id) {
                $this.data('lojas', usuario.lojas);
                return;
            }
        });
    });

    $('#modelo-usuario-id').attr('disabled', false);

    $('#modelo-usuario-id').change(function() {
        var $this = $(this);

        $('.container-lojas').find('.check-loja').prop('checked', false);

        if ($this.val()) {
            var lojas = $this.find('option:selected').data('lojas');
            console.log('lojas', lojas);
            $.each(lojas, function(index, loja) {
                console.log('ID', '#'+loja.id+'-loja-id');
                var $loja = $('#'+loja.id+'-loja-id');
                $loja.prop('checked', true);
            });
        }

        // Faço toggle em todos
        $('.check-loja').each(function() {
            toggleLojaExtraOptions($(this), true);
        });
    });

    $('.btn-marcar-lojas-toggle').click(function() {
        var $this = $(this);
        var $fa = $this.find('.fa');

        if ($fa.hasClass('fa-check-square-o')) {
            $this.html('<span class="fa fa-square-o"></span> Desmarcar todas');
            $('.container-lojas').find('input[type="checkbox"]').prop('checked', true);
        } else {
            $fa.addClass('fa-check-square-o');
            $this.html('<span class="fa fa-check-square-o"></span> Marcar todas');
            $('.container-lojas').find('input[type="checkbox"]').prop('checked', false);
        }
    });


    $('.check-loja').each(function() {
        toggleLojaExtraOptions($(this), false);
    });
    function toggleLojaExtraOptions($this, transition) {
        var $selector = $this.parents('.checkbox').next('#container-loja-extra-options');
        if ($this.prop('checked')) {
            if (transition) {
                $selector.stop().slideDown();
            } else {
                $selector.stop().show();
            }

        } else {
            if (transition) {
                $selector.stop().slideUp();
            } else {
                $selector.stop().hide();
            }
        }
    }
    $('.check-loja').click(function() {
        var $this = $(this);
        toggleLojaExtraOptions($this, true);
    });

    // Exibir alert que a loja não tem todos os setores da checklist
    $('#modelo-checklist-id').change(function() {
        toggleAlert($(this));
    });

    function toggleAlert($element) {
        // Pega setores das lojas
        $('.container-loja').each(function() {

            if (!$element.val()) {
                $(this).find('.alert-diferenca-setores').fadeOut('fast');
                return;
            }

            var setoresDaChecklist = getSetoresDaChecklist($element.val());

            var setoresDaLoja = $.parseJSON($(this).find('#loja-setores').val());
            var diferenca = [];
            var setoresDaLojaIds = [];
            $.each(setoresDaLoja, function(k, setor) {
                setoresDaLojaIds.push(setor.id);
            });
            $.each(setoresDaChecklist, function(k, setor) {
                console.log('ID está?', setor.id);
                console.log('Lojas ids', setoresDaLojaIds);
                console.log('Result inArray', $.inArray(setor.id, setoresDaLojaIds));
                if ($.inArray(setor.id, setoresDaLojaIds) < 0) {
                    diferenca.push(setor.nome);
                }
            });
            console.log('Setores da Checklist', setoresDaChecklist);
            console.log('Setores da Loja', setoresDaLoja);
            console.log('Diferença', diferenca);
            console.log('Length', diferenca.length);
            if (diferenca.length > 0) {
                console.log('Mostra vista', diferenca.join(' | '));
                $(this)
                    .find('.alert-diferenca-setores')
                    .fadeIn('fast')
                    .find('.kode-alert')
                    .html('<span class="fa fa-exclamation-triangle"></span> Esta loja não possui todos os setores da Checklist e aparecerá de forma parcial para o auditor. Os setores são (<strong>'+diferenca.join(' | ')+'</strong>).');
                console.log('A loja tem tais diferenças', diferenca);
            } else {
                $(this).find('.alert-diferenca-setores').fadeOut('fast');
            }
        });
    }

    function getSetoresDaChecklist(id) {
        var out = null;
        $.each(checklistComSetores, function(key, value) {
            if (value.id == id) {
                var setores = [];
                $.each(value.ordem_setores, function(k, v) {
                    console.log('SETOR', v);
                    setores.push(v.setor)
                });

                out = setores;
                return out;
            }
        });

        return out;
    }

});
