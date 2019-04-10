<?php

class sebtestPaymentsMethod extends iPizzaPaymentsMethod
{
    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }
}