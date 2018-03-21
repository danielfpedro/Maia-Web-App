<div class="row">
	<div class="col-md-12">
		<h5>Questionário</h5>
	</div>
	<div class="col-md-12">
		<?= h($resposta->pergunta->checklist->nome) ?>
	</div>
	<div class="col-md-12">
		<h5>Auditor</h5>
	</div>
	<div class="col-md-12">
		<?= h($resposta->visita->usuario->nome) ?>
	</div>

	<div class="col-md-12">
		<h5>Local</h5>
	</div>
	<div class="col-md-12">
		[<?= h($resposta->pergunta->setor->nome) ?>] <?= h($resposta->visita->loja->nome) ?>
	</div>

	<div class="col-md-12">
		<h5>Pergunta</h5>
	</div>
	<div class="col-md-12">
		<?= h($resposta->pergunta->pergunta) ?>
	</div>

	<div class="col-md-12">
		<h5>Resposta</h5>
	</div>
	<div class="col-md-12">
		<?= h($resposta->alternativa_selecionada->alternativa) ?>
		<br>
		<em>em <?= $resposta->criado_em->format('d/m/y') ?></em>
	</div>

	<div class="col-md-12">
		<h5>Observação</h5>
	</div>
	<div class="col-md-12">
		<p>
			<?= ($resposta->observacao) ? h($resposta->observacao) : '-' ?>
		</p>
	</div>

	<?php if ($resposta->fotos_requeridas): ?>
		<?php foreach ($resposta->fotos_requeridas as $foto): ?>
			<div class="col-md-2">
			</div>
		<?php endforeach ?>
	<?php endif ?>

</div>