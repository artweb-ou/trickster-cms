<?php

class shoppingBasketStepElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        'shoppingBasketStepProducts',
        'shoppingBasketStepDiscounts',
        'shoppingBasketStepDelivery',
        'shoppingBasketStepTotals',
        'shoppingBasketStepPayments',
        'shoppingBasketStepAccount',
        'shoppingBasketStepCheckoutTotals',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }
}

