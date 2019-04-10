<?php

class shoppingbasketserviceElement extends structureElement
{
    public $dataResourceName = 'module_shoppingbasketservice';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $connectedProductsIds;
    protected $connectedProducts;
    protected $connectedCategories;
    protected $connectedCategoriesIds;
    protected $connectedDeliveryTypes;
    protected $connectedDeliveryTypesIds;
    protected $connectedShoppingBasketServices;
    protected $connectedShoppingBasketServicesIds;
    protected $deliveryTypesShoppingBasketServicesIndex;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['price'] = 'money';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }
}

