<?php foreach ($logs as $log): ?>
	<div class="row table-grid">
		<div class="col-md-4">
			<?= h($log->usuario->short_name) ?>
		</div>
		<div class="col-md-4">
			<span class="<?= $log->planos_taticos_logs_tipo->icon ?>"></span>&nbsp;<?= h($log->planos_taticos_logs_tipo->nome) ?>
		</div>
		<div class="col-md-4">
			<?= h($log->criado_em->format('h:i d/m/y')) ?>
		</div>
	</div>
<?php endforeach ?>

<?php if (!$logs): ?>
	<div class="kode-alert alert6">
		Nenhum log para mostrar.
	</div>
<?php endif ?>