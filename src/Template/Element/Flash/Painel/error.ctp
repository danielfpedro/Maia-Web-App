<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="kode-alert alert6 alert-custom alert-autoclose">
    <h4><span class="fa fa-times-circle"></span> Erro!</h4>
    <?= $message ?>
    <a href="#" class="closed">&times;</a>
</div>
