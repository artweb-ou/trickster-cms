<?php

include_once dirname(__FILE__) . '/dbServiceContainer.php';

class statsDbServiceContainer extends dbServiceContainer
{
    public function makeInstance()
    {
        return $this->loadDb('statstransport')
            ?: $this->registry->getService('db');
    }

    public function makeInjections($instance)
    {
    }
}

