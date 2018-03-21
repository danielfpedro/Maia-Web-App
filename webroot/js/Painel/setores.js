$(function() {
    $containerAlternativas = $('#container-alternativas');
    $containerAlternativasProto = $containerAlternativas.clone();

    $alternativaLinhaProto = $containerAlternativasProto.find('.alternativa-linha');

    $containerAlternativas
        .appendTo('#carrega-alternativas > div')
        .fadeIn();

    $containerAlternativas.find('#container-campos').sortable({
        axis: 'y',
        opacity: 0.5,
        containment: 'parent',
        tolerance: "pointer",
        // handle: ".handle",
        cancel: 'input, button',
    });

    limpaAlternativas();

    carregaAlternativasDoBanco();
    function carregaAlternativasDoBanco() {
        var alternativas = $.parseJSON($('#alternativas').val());
        var qtdCriar = alternativas.length - 2;
        if (alternativas.length > 0) {
            $.each(alternativas, function(index, alternativa) {
                addOpcao(alternativa);
            });
        }
        if (qtdCriar < 0) {
            addOpcao();
            addOpcao();
        }
    }

    $('.btn-add-questao').click(function() {
        addOpcao();
    });

    $('#container-alternativas').on('click', '.btn-remove-questao', function() {

        var $this = $(this);
        var $linha = $this.parents('.alternativa-linha');

        $linha.slideUp('fast', function() {
            $(this).remove();
            if (contaAlternativas() <= 2) {
                $containerAlternativas.find('#container-campos').find('.btn-remove-questao').attr('disabled', true);
            }
        });
    });

    function contaAlternativas() {
        return $containerAlternativas.find('#container-campos > .alternativa-linha').length;
    }

    function addOpcao(valores) {
        var uid = guid();

        if (typeof valores == 'undefined') {
            valores = {
                id: null,
                alternativa: null,
                valor: null,
                temFoto: false,
                itemCritico: false
            }
        }

        console.log('Valores', valores);
        $alternativaLinha = $alternativaLinhaProto.clone();

        $alternativaLinha
            .hide()
            .appendTo($containerAlternativas.find('#container-campos'));

        $alternativaLinha.slideDown('fast');

        $alternativaLinha
            .find('.pergunta-id')
            .attr('name', 'alternativas['+uid+'][id]')
            .val(valores.id);

        $alternativaLinha
            .find('.pergunta')
            .attr('name', 'alternativas['+uid+'][alternativa]')
            .val(valores.alternativa);
        $alternativaLinha
            .find('.valor')
            .attr('name', 'alternativas['+uid+'][valor]')
            .val(valores.valor);
        $alternativaLinha
            .find('.tem-foto')
            .attr('name', 'alternativas['+uid+'][tem_foto]')
            .attr('id', 'tem-foto' + uid)
            .prop('checked', (parseInt(valores.tem_foto)));

        $alternativaLinha
            .find('.label-tem-foto')
            .attr('for', 'tem-foto' + uid);

        $alternativaLinha
            .find('.item-critico')
            .attr('name', 'alternativas['+uid+'][item_critico]')
            .attr('id', 'item-critico' + uid)
            .prop('checked', (parseInt(valores.item_critico)));
        $alternativaLinha
            .find('.label-item-critico')
            .attr('for', 'item-critico' + uid);

        if (contaAlternativas() > 2) {
            $containerAlternativas.find('#container-campos').find('.btn-remove-questao').attr('disabled', false);
        }
    }

    function validaAlternativas() {
        var out = true;

        $containerAlternativas.find('#container-campos > .alternativa-linha').each(function () {
            var $this = $(this);
            var $pergunta = $this.find('.pergunta');
            var $valor = $this.find('.valor');

            if (!$pergunta.val()) {
                $pergunta.parent('div').addClass('has-error');
                out = false;
            } else {
                $pergunta.parent('div').removeClass('has-error');
            }
        });

        $containerAlternativas.find('#container-campos > .alternativa-linha').each(function () {
            var $this = $(this);
            var $pergunta = $this.find('.pergunta');
            var $valor = $this.find('.valor');

            if (!$pergunta.val()) {
                $pergunta.focus();
                return false;
            }
        });

        return out;
    }

    function limpaAlternativas() {
        $containerAlternativas.find('#container-campos').html('');
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

    $('form').submit(function() {
        return validaAlternativas();
    });

});
