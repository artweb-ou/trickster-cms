<?php

class shoppingBasketStepProductsElement extends structureElement
{
    use ShoppingBasketStepContentTrait;
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }
}

