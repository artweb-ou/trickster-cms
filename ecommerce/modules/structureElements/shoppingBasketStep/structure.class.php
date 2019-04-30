<?php

class shoppingBasketStepElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_shoppingbasket_step';
    protected $allowedTypes = [
        'login',
        'registration',
        'shoppingBasketStepProducts',
        'shoppingBasketStepDiscounts',
        'shoppingBasketStepDelivery',
        'shoppingBasketStepTotals',
        'shoppingBasketStepPayments',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }
}

