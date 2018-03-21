$(function() {
	
	$checklists = $('#checklist');
	$setores = $('#setores');
	$perguntas = $('#perguntas');

	// Para controlar oq já carregou ajax
	var acabou = {setores: false, perguntas: false};

	var perguntasAjaxRequest = null;
	var setoresAjaxRequest = null;

	var checklistsComSetores = ($('#questionarios-com-setores').val()) ? $.parseJSON($('#questionarios-com-setores').val()) : [];

	// Quando eu filtro as perguntas na combo dele eu removo entao eu preciso
	// de todas aqui para voltar quando precisar
	var todasAsPerguntas = [];

	init();

	function init(){
		// Desabilito setores e perguntas
		$setores.attr('disabled', true);
		$perguntas.attr('disabled', true);

		carregaComboSetores();
		carregaComboPerguntas();
	}

    $checklists.change(function() {
        carregaComboSetores();
        carregaComboPerguntas();
    });

    function acabouSetoresEPerguntas() {
    	console.log('Acabou os dois');
		filtraPerguntas();
		$setores.on('change', filtraPerguntas);
    }

    function carregaComboSetores() {
    	var urlSetores = $checklists.data('url-carrega-setores');
    	urlSetores = urlSetores.replace(':checklistId', parseInt($checklists.val()));

    	setoresAjaxRequest = (setoresAjaxRequest) ? setoresAjaxRequest.abort() : null;
    	setoresAjaxRequest = carregaDadosCombo(urlSetores, $setores, 'setores');
    }

	function carregaComboPerguntas() {
    	var urlPerguntas = $checklists.data('url-carrega-perguntas');
    	urlPerguntas = urlPerguntas.replace(':checklistId', parseInt($checklists.val()));

		perguntasAjaxRequest = (perguntasAjaxRequest) ? perguntasAjaxRequest.abort() : null;
    	perguntasAjaxRequest = carregaDadosCombo(urlPerguntas, $perguntas, 'perguntas');
    }

    function filtraPerguntas() {
    	var perguntasSelecionadas = $perguntas.select2('data');
    	
    	perguntasSelecionadas = perguntasSelecionadas.map(function(data) {
    		return data.id;
    	});

    	console.log('Perguntas SELECIONADAS', perguntasSelecionadas);
    	perguntasFiltradas = todasAsPerguntas;
    	if ($setores.val()) {
	    	perguntasFiltradas = todasAsPerguntas.filter(function(setor) {
	    		achou = false;
	    		$.each($setores.val(), function(key, value) {
	    			if (value == setor.id) {
	    				achou = true;
	    				return
	    			}
	    		});

	    		return achou;
	    	});
    	}

    	perguntasFiltradas.map(function(setor) {
    		setor.children.map(function(pergunta) {
    			pergunta.selected = false;
    			if (perguntasSelecionadas.indexOf(pergunta.id.toString()) >= 0) {
    				pergunta.selected = true;
    			}
    			return pergunta;
    		});
    	});

    	$perguntas
    	.empty()
    	.select2({
    		placeholder: 'Todas',
    		data: perguntasFiltradas
    	})
    	.trigger('change');
    	console.log('Perguntas filtradas', perguntasFiltradas);
    }

    function getSetorByIdDasPerguntas(id) {

    }

    function carregaDadosCombo(url, $alvo, tipo) {

		$alvo.empty();

    	if ($checklists.val()) {
	    	$alvo.select2({placeholder: 'Carregando, aguarde...'}).attr('disabled', true);

	    	return $.getJSON(url, function(data) {
	    		if (tipo == 'perguntas') {
	    			todasAsPerguntas = data;
	    		}

	    		// var loadData = ($alvo.data('value')) ? $alvo.data('value').toString().split(' ') : [];
	    		
	    		setoresQueryStringValueToArray = (typeof $alvo.data('value') != 'undefined') ? $alvo.data('value').toString().split(' ') : [];
	    		if (tipo == 'setores') {
		    		$.each(data, function(k, setor) {
		    			console.log('query string array', setoresQueryStringValueToArray);
		    			setor.selected = (setoresQueryStringValueToArray.indexOf(setor.id.toString()) >= 0);
		    		});
	    		} else if(tipo == 'perguntas') {
		    		$.each(data, function(k, setor) {
		    			console.log('Setor', setor);
		    			$.each(setor.children, function(k, c) {
							c.selected = (setoresQueryStringValueToArray.indexOf(c.id.toString()) >= 0);
		    			});
		    		});
	    		}

	    		$alvo
	    			.select2({data: data})
	    			.trigger('change');

	    		if (tipo == 'perguntas') {
	    			acabou.perguntas = true;
	    		} else if(tipo == 'setores') {
					acabou.setores = true;
	    		}

	    		if (acabou.perguntas && acabou.setores) {
	    			acabouSetoresEPerguntas();
	    		}
	    	})
	    	.error(function(error, b) {
				if (b != 'abort') {
					alert('Ocorreu um erro ao carregar os dados, favor atualizar a página e tentar novamente.');	
				}
	    	})
	    	.always(function() {
				$alvo.select2({placeholder: 'Todos'}).attr('disabled', false);
	    	});
    	} else {
			$alvo.select2({placeholder: 'Selecione o Questionário'}).attr('disabled', true);    		
    	}

    	return null;
    }

});