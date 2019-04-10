<?php

class danskebankPaymentsMethod extends iPizzaPaymentsMethod
{
    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }
}