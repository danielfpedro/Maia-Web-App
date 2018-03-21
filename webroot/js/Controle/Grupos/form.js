$(function() {

	$('#navbar-logo-file-placeholder').change(function() {
	  var preview = document.querySelector('img');
	  var file    = this.files[0];
	  var reader  = new FileReader();

	  reader.onloadend = function () {
	    $('.applogo > img').attr('src', reader.result);
	  }

	  if (file) {
	    reader.readAsDataURL(file);
	  } else {
	    preview.src = "";
	  }
	});

	// $('#logo-painel-size').keyup(function() {
	// 	atualizaNavbarLogoMarginTop($(this).val());
	// });
	atualizaNavbarLogoWidth($('#navbar-logo-width').val());
	$('body').on('keyup mouseup', '#navbar-logo-width', function() {
		atualizaNavbarLogoWidth($(this).val());
	});
	atualizaNavbarLogoMarginTop($('#navbar-logo-margin-top').val());
	$('body').on('keyup mouseup', '#navbar-logo-margin-top', function() {
		atualizaNavbarLogoMarginTop($(this).val());
	});

	// Cores
	atualizaNavbarColor($('#navbar-color').val(), true);
	atualizaNavbarFontColor($('#navbar-font-color').val(), true);

	$('#painel-navbar-font-color').keyup(function() {
		atualizaNavbarFontColor();
	});
});

function atualizaNavbarLogoWidth(value) {
	if (value) {
		$('.applogo > img').css('width', value + 'px');	
	}
}
function atualizaNavbarLogoMarginTop(value) {
	if (value) {
		$('.applogo > img').css('margin-top', value + 'px');	
	}
}


function atualizaNavbarFontColor(color, isColorHex) {
	if (typeof color == 'undefined') {
		return false;
	}
	isColorHex = (typeof isColorHex == 'undefined') ? false : true;
	color = (isColorHex) ? color: color.toHEXString();
	$('.dropdown-profile .profilebox,.dropdown-profile .profilebox > .caret,.sidebar-open-button-mobile').css('color', color);
}
function atualizaNavbarColor(color, isColorHex) {
	if (typeof color == 'undefined') {
		return false;
	}

	isColorHex = (typeof isColorHex == 'undefined') ? false : true;
	color = (isColorHex) ? color: color.toHEXString();
	console.log('COLOR', color);
	$('#top').css('background-color', color);
}