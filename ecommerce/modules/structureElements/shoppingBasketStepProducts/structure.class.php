<?php

class shoppingBasketStepProductsElement extends structureElement
{
    use ShoppingBasketStepContentTrait;
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_shoppingbasket_step_products';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
    }
}

