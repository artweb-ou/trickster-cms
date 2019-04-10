<?php

class orderDiscountElement extends structureElement
{
    public $dataResourceName = 'module_order_discount';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['discountId'] = 'text';
        $moduleStructure['discountCode'] = 'text';
        $moduleStructure['value'] = 'text';
    }
}