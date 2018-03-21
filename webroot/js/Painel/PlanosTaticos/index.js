$(function() {
	$('.check-alterar-completo').click(function() {
		var $this = $(this);

		var url = $this.data('url');
		console.log('Teste', url);

		var flag = ($this.prop('checked')) ? 1 : 0;
		var url = url.replace(':flag', flag);

        swal({
              type: 'info',
              title: 'Atenção!',
              html: 'Você realmente deseja alterar o status desta atividade?',
              showCancelButton: true,
              confirmButtonColor: '#F15949',
              confirmButtonText: 'Alterar',
              showLoaderOnConfirm: true,
              preConfirm: function () {
                return new Promise(function (resolve, reject) {
 					$.getJSON(url, function() {
	 						resolve();
	 					})
						.fail(function() {
							reject();

							$this.prop('checked', !($this.prop('checked')));
						});
                }, function() {
                	console.log('sd');
                });
              },
              allowOutsideClick: false
            })
        	.then(function () {
            	console.log('CANCELOU!');
              swal({
                type: 'success',
                title: 'Sucesso!',
                html: 'Status Alterado, Por favor, aguarde...',
                showConfirmButton: false
               });
                location.reload();
          	}, function() {
				$this.prop('checked', !($this.prop('checked')));
          	});
	});

	
});