<?php
	echo $this->Html->script('bim/bim', ['block' => true]);
?>
<input type="text" bim-model="nome">

<span bim-print="nome"></span>

<input type="text" bim-model="nome">

<div bim-for="item items">
	<span data-bim-print="item.nome"></span>	
</div>