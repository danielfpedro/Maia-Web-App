<div class="pi">
<div class="page-header-flex">
    
    <ol class="octopo-breadcrumb">
        <?php if (isset($items)): ?>
            <?php foreach ($items as $key => $item): ?>
                <li>
                    <?= $this->Html->link($key, $item); ?>
                </li>
            <?php endforeach; ?>
        <?php endif ?>
        <li class="active">
            <?= $this->fetch('breadcrumbTitle') ?>
        </li>
    </ol>

    <div class="flex-item-right">
        <?= $this->fetch('breadcrumbButtonsRight') ?>
    </div>
</div>
</div>