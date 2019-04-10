<?php

class massModifyCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->massModifyProducts();
            $destination = $controller->fullURL;
            if (!empty($_SERVER['QUERY_STRING'])) {
                $destination .= '?' . $_SERVER['QUERY_STRING'];
            }
            $controller->redirect($destination);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'massEditMethod',
            'newCategories',
            'newDiscounts',
            'newBrand',
            'targets',
            'targetAll',
            'brand',
            'discount',
            'category',
            'productPriceMultiplier',
            'productPriceAddition',
        ];
    }

    public function getElementsByIdList($structureManager, $ids)
    {
        $result = [];
        foreach ($ids as $id) {
            if ($element = $structureManager->getElementById($id)) {
                $result[] = $element;
            }
        }
        return $result;
    }
}