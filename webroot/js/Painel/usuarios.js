$(function() {
    $('#cargo-id').change(function() {
        var $this = $(this);

        if ($this.val() == 2) {
            $('.painel-lojas').slideDown();
        } else {
            $('.painel-lojas').slideUp();
        }
    });
});
