<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="kode-alert alert3">
    <?= $message ?>
</div>
