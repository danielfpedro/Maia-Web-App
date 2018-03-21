angular.module('painel', [])
  .controller('LojaFormController', function() {
    var $lojaForm = this;

    $lojaForm.toc = '';
    $lojaForm.estados = [
    	{nome: 'Rio'},
    	{nome: 'Sao Paulo'}
    ];

	console.log($lojaForm.toc);

    $lojaForm.tey = function() {
		console.log('Todos', $lojaForm.toc);
    }
    // $http.get()
 
  });