<?php

class productGalleryProductElement extends structureElement
{
    public $dataResourceName = 'module_productgallery_product';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $formFieldsList;
    const LINK_TYPE_PRODUCT = 'productGalleryProduct';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['positionX'] = 'text';
        $moduleStructure['positionY'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['code'] = 'text';
        $moduleStructure['price'] = 'money';
        $moduleStructure['productIds'] = 'numbersArray';
    }

    public function getConnectedProductsIds()
    {
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, self::LINK_TYPE_PRODUCT, 'child');
    }

    public function getConnectedProducts()
    {
        $result = [];
        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('languagesManager');
        foreach ($this->getConnectedProductsIds() as $id) {
            $result[] = $structureManager->getElementById($id, $languagesManager->getCurrentLanguageId());
        }
        return $result;
    }

    public function getConnectedProductElements()
    {
        $result = [];
        $structureManager = $this->getService('structureManager');
        foreach ($this->getConnectedProductsIds() as $id) {
            $element = $structureManager->getElementById($id);
            $product = [];
            $product['id'] = $element->id;
            $product['title'] = $element->getTitle();
            $product['select'] = true;
            $result[] = $product;
        }
        return $result;
    }

    public function getTitle()
    {
        $titles = [];
        foreach ($this->getConnectedProducts() as $product) {
            $titles[] = $product->title;
        }
        if ($titles) {
            return implode(', ', $titles);
        }
        return parent::getTitle();
    }

}