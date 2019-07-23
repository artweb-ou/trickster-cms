<?php

class shoppingBasketStepElement extends structureElement
{
    use ConfigurableLayoutsProviderTrait;

    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_shoppingbasket_step';
    protected $allowedTypes = [
        'shoppingBasketStepAgreement',
        'shoppingBasketStepProducts',
        'shoppingBasketStepDiscounts',
        'shoppingBasketStepDelivery',
        'shoppingBasketStepPromo',
        'shoppingBasketStepTotals',
        'shoppingBasketStepServices',
        'shoppingBasketStepPayments',
        'shoppingBasketStepAccount',
        'shoppingBasketStepCheckout',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
    }

    public function getStepElements()
    {
        return $this->getChildrenList();
    }

    public function getStepElementByType($type)
    {
        $structureType = 'shoppingBasketStep' . ucfirst($type);
        foreach ($this->getStepElements() as $element) {
            if ($element->structureType == $structureType) {
                return $element;
            }
        }
        return false;
    }

    public function getStepUrl() {
        $parentElement = $this->getCurrentParentElement();
        if(!empty($parentElement)) {
            $url = $parentElement->URL.'step:'.$this->structureName.'/';
            return $url;
        }
        return false;
    }
}

