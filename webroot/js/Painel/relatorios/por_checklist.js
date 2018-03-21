$(function(){

    // Data pra popular, quando a gente limpa o filtro setores e repopular as perguntas no total
    var todasPerguntasAtuais = [];
    
    // $('#setores').change(function() {
    //     var $this = $(this);
    //     filtraPerguntasPorSetores($this);
    // });

    // function filtraPerguntasPorSetores($this) {
    //     $perguntas = $('#perguntas');

    //     data = [];

    //     var idsPerguntasSelecionadas = $perguntas.select2('data').map(function(value) {
    //         return parseInt(value.id);
    //     });

    //     if ($this.val()) {
    //         // console.log('Questionario com setores', checklistsComSetores);
    //         console.log('Setores selecionados', $this.select2('data'));
    //         var idsSetoresSelecionados = $this.select2('data').map(function(value) {
    //             return parseInt(value.id);
    //         });
    //         console.log('IDS', idsSetoresSelecionados);
    //         $.each(todasPerguntasAtuais, function(key, setor) {
    //             console.log('Setor', setor);
    //             if ($.inArray(setor.id, idsSetoresSelecionados) >= 0) {
    //                 $.each(setor.children, function(keyChild, child) {
    //                     child.selected = ($.inArray(child.id, idsPerguntasSelecionadas) >= 0);
    //                 });
    //                 console.log('Setor add', setor);
    //                 data.push(setor);
    //             }
    //         });
    //     } else {
    //         $.each(todasPerguntasAtuais, function(key, setor) {
    //             $.each(setor.children, function(keyChild, child) {
    //                 child.selected = ($.inArray(child.id, idsPerguntasSelecionadas) >= 0);
    //             });
    //             data.push(setor);
    //         });
    //     }




    //     console.log('Data para add', data);
    //     $perguntas
    //         .empty()
    //         .trigger('change')
    //         .select2({
    //             'placeholder': 'Todas',
    //             data: data
    //         });
    // }

    // Btn que recocle e mostra todos os setores
    $('.btn-toggle-todos-setores').click(function() {
        var $this = $(this);
        if (parseInt($this.data('para-expandir')) == 1) {
            $('[id^="linha-setor-"]')
                .data('is-expandido', 1)
                .show();

            $this
                .html('<span class="fa fa-minus"></span>')
                .data('para-expandir', 0);

            $('.relatorio-toggle-setor')
                .html('<span class="fa fa-minus"></span>');
        } else {
            $('[id^="linha-setor-"]')
                .data('is-expandido', 0)
                .hide();

            $this
                .html('<span class="fa fa-plus"></span>')
                .data('para-expandir', 1);

            $('.relatorio-toggle-setor')
                .html('<span class="fa fa-plus"></span>');
        }
    });

    $('.relatorio-toggle-setor').each(function() {
        var $this = $(this);
        var setorId = $this.data('setor-id')
        var $linhas = $('tr#linha-setor-' + setorId);

        var expandir = false;
        if ($this.data('is-expandido') == 1) {
            expandir = true;
            $this
                .html('<span class="fa fa-minus"></span>');
        } else {
            $this
                .html('<span class="fa fa-plus"></span>');
        }
        $linhas.each(function() {
            if (expandir) {
                $this.
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('.relatorio-toggle-setor').click(function() {
        var $this = $(this);
        var setorId = $this.data('setor-id')
        var $linhas = $('tr#linha-setor-' + setorId);

        // Btns pq tenho botao no header e no rodape do setor entao tenho que udar o icone de ambos
        var $btns = $('.relatorio-toggle-setor[data-setor-id="'+setorId+'"]');

        var newIcon = null;

        $linhas.each(function() {
            if (parseInt($(this).data('is-expandido')) == 1) {

                $(this)
                    .data('is-expandido', 0)
                    .hide();

                newIcon = "fa-plus";
            } else {
                $(this)
                    .data('is-expandido', 1)
                    .show();

                newIcon = "fa-minus";
            }
        });

        $btns
            .find('span')
            .removeClass('fa-minus')
            .removeClass('fa-plus')
            .addClass(newIcon);


        $(this).blur();
    });
});
