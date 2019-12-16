<?php

class shoppingBasketStatusElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_shoppingbasketstatus';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $basketService;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['popup'] = 'checkbox';
        $moduleStructure['floating'] = 'checkbox';
    }

    protected function getBasketService()
    {
        if ($this->basketService === null) {
            $this->basketService = $this->getService('shoppingBasket');
        }
        return $this->basketService;
    }

    public function getTotalPrice()
    {
        if ($service = $this->getBasketService()) {
            return $service->getTotalPrice();
        }
        return 0;
    }

    public function getProductsAmount()
    {
        if ($service = $this->getBasketService()) {
            return $service->getProductsAmount();
        }
        return 0;
    }
}


