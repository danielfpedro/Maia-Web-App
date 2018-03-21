<div class="row">
    <div class="col-md-12" style="margin-top: 22px;">
        <ul class="pagination pagination-sm">
            <?php
                echo '<li class="pull-left" style="margin-right: 10px; margin-top: 2px;">' . $this->Paginator->counter('Página {{page}}/{{pages}} de {{count}} registro(s)') . '</li>';
                echo $this->Paginator->first($this->Html->icon('step-backward'), ['escape' => false]);
                echo $this->Paginator->prev($this->Html->icon('chevron-left'), ['escape' => false]);
                echo $this->Paginator->numbers();
                echo $this->Paginator->next($this->Html->icon('chevron-right'), ['escape' => false]);
                echo $this->Paginator->last($this->Html->icon('step-forward'), ['escape' => false]);
            ?>
        </ul>
    </div>
</div>
