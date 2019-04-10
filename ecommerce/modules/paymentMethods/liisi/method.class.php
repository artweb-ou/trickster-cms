<?php

class liisiPaymentsMethod extends iPizzaPaymentsMethod
{
    protected $requestType = 'post';

    protected function setClassFilePath()
    {
        $this->classFilePath = dirname(__FILE__);
    }
}