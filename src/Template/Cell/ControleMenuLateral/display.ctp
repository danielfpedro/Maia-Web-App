<div class="sidebar">
    <?php foreach ($menuItems as $menuName => $items): ?>
        <ul class="sidebar-panel nav">
            <li class="sidetitle"><?= $menuName ?></li>
            <?php foreach ($items as $key => $item): ?>
                <?php
                    $active = '';
                    if (
                        (
                            $this->request->controller == $item['url']['controller'] &&
                            $this->request->action == $item['url']['action']
                        ) ||
                        (
                            isset($item['internas']) && in_array(['controller' => $this->request->controller, 'action' => $this->request->action], $item['internas'])
                        )

                    ) {
                        $active = 'active';
                    };

                ?>
                <li class="">
                    <?= $this->Html->link('<span class="icon color5"><i class="fa fa-' .$item['icon']. '"></i></span>' .$item['label'], $item['url'], ['class' => '' . $active, 'escape' => false]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>
