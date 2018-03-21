$(function() {
    var somaThFixedWidth = 0;
    $('.table-content-fixed-with > thead > tr > th').each(function() {
        console.log('aqui th');
        var width = ($(this).data('width')) ? $(this).data('width') : 150;
        somaThFixedWidth += width;
        $(this).css('width', width + 'px');
    });
    $('.table-content-fixed-with').css('width', somaThFixedWidth + 'px');
});
