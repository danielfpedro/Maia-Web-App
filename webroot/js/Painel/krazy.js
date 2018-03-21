$(function() {

	$('.krazy-btn-delete').click(function(event) {
		event.preventDefault()

		var $this = $(this);
		var url = $this.attr('krazy-url');
		var message = $this.attr('krazy-message');

		var data = $this.data();

		swal({
			type: 'warning',
			title: 'Deletar',
			html: message,
			showCancelButton: true,
			showLoaderOnConfirm: true,
			allowOutsideClick: true,
			preConfirm: () => {
			    return new Promise((resolve, reject) => {
			    	$.ajax({
				      	type: 'delete',
				      	url: url,
				      	data: data,
				      	success: function(success) {
				      		resolve(success);
				      	},
				      	error: function(error) {
							swal({
								type: 'error',
								title: 'Não foi deletado',
								text: 'Ocorreu um erro, por favor tente novamente'
							});
					    }
				    });
			    });
			},
		}).then(function() {
			swal({
				type: 'success',
				title: 'Deletado',
				text: 'Por favor, aguarde...',
				showConfirmButton: false
			});
			location.reload();
		}, function() {
			
		});
	});


	$('.krazy-external-link').each(function() {
		$(this)
			.html('<span class="fa fa-external-link-alt"></span> ' + $(this).html())
			.attr({
				'title': 'O link será aberto em uma nova aba.',
				'target': '_blank'
			});
	});
});