<?php

class shoppingBasketStepAccountElement extends structureElement
{
    use ShoppingBasketStepContentTrait;
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        'login',
        'registration',
    ];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    public function getLoginForm()
    {
        $result = null;
        foreach ($this->getChildrenList() as $childElement) {
            if ($childElement->structureType != 'login') {
                continue;
            }
            $result = $childElement;
        }
        return $result;
    }

    public function getRegistrationForm()
    {
        $result = null;
        foreach ($this->getChildrenList() as $childElement) {
            if ($childElement->structureType != 'registration') {
                continue;
            }
            $result = $childElement;
        }
        return $result;
    }
}

