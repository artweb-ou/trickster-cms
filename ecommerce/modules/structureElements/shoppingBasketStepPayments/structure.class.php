<?php

class shoppingBasketStepPaymentsElement extends structureElement
{
    use ShoppingBasketStepContentTrait;
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_shoppingbasket_step_payments';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
    }

    public function getValidators($formData = [])
    {
        $validators = [];
        $validators['paymentMethodId'][] = 'notEmpty';
        return $validators;
    }
}

