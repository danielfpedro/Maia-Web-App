<div class="sidebar">
    <?php foreach ($menu as $menuName => $items): ?>
        <ul class="sidebar-panel nav">
            <li class="sidetitle"><?= $menuName ?></li>
            <?php foreach ($items as $key => $item): ?>
                <li class="">
                    <?= $this->Html->link('<span class="icon color5"><i class="fa fa-' .$item['icon']. '"></i></span>' .$item['label'], $item['url'], ['class' => ($item['active']) ? 'active' : null, 'escape' => false]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>
