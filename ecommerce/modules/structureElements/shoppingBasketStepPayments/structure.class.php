<?php

class shoppingBasketStepPaymentsElement extends structureElement
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

    public function getValidators($formData = [])
    {
        $validators = [];
        $validators['paymentMethodId'][] = 'notEmpty';
        return $validators;
    }
}

