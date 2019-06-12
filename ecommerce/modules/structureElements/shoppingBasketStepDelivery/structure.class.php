<?php

class shoppingBasketStepDeliveryElement extends structureElement
{
    use ShoppingBasketStepContentTrait;
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_shoppingbasket_step_delivery';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
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

