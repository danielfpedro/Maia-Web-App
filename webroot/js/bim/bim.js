var _bimPrefix = 'bim';
var $bim = {
	do: function() {
		$('input['+_bimPrefix+'-model]').each(function() {
			console.log('Aqui input');
			var $this = $(this);
			$this.val($bim[$this.attr(bimGetPrefix('model'))]);
		});

		$('['+_bimPrefix+'-print]').each(function() {
			var $this = $(this);
			// console.log('PRINT', $bim[$this.text()]);
			$this.text($bim[$this.attr('bim-print')]);
		});
	}
};

function bimGetPrefix(selector) {
	var out = _bimPrefix + '-' + selector;
	console.log('OUT', out);

	return out;
}

$(document).on('keyup', 'input[bim-model]', function() {
	var $this = $(this);

	console.log('Valor do bim model', $this.attr('bim-model'));
	$bim[$this.attr('bim-model')] = $this.val();
	console.log('$BIM', $bim);
	$bim.do();
});

$(function() {

	$bim.nome = 'Daniel de Faria Pedro';
	$bim.items = [
		{nome: 'A'},
		{nome: 'B'},
	];
	$bim.do();

});