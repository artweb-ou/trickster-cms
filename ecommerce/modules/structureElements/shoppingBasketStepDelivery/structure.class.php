<?php

class shoppingBasketStepDeliveryElement extends structureElement
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

    public function getValidators($formData = []) {
        $validators = [];
        $receiverIsPayer = true;
        if (!isset($formData['receiverIsPayer']) || $formData['receiverIsPayer'] != '1') {
            $receiverIsPayer = false;
        }
        if (!$receiverIsPayer) {
            $validators['payerFirstName'][] = 'notEmpty';
            $validators['payerLastName'][] = 'notEmpty';
            $validators['payerPhone'][] = 'notEmpty';
            $validators['payerEmail'][] = 'email';
        }
        return $validators;
    }

    public function useCustomFields() {
        return true;
    }
}

