<?php

class sebPaymentsMethod extends iPizzaPaymentsMethod
{
    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }
}