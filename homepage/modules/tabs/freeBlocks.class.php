<?php

class freeBlocksTab extends Tab
{
    use TabTrait;

    protected function init()
    {
        $this->action = 'showFullList';
        $this->view = 'freeBlocks';
        $this->icon = 'icon_bfreeBlocks';
    }
}