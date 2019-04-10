<?php

class receiveGenericIcon extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            foreach ($structureElement->getMultilanguageDataChunk('image') as $languageId => $dataChunk) {
                if ($dataChunk->originalName) {
                    $structureElement->setValue('image', $structureElement->id . '_' . $languageId, $languageId);
                    $structureElement->setValue('originalName', $dataChunk->originalName, $languageId);
                }
            }

            $structureElement->persistElementData();

            $structureElement->updateConnectedProducts($structureElement->products);
            $structureElement->updateConnectedCategories($structureElement->categories);
            $structureElement->updateConnectedBrands($structureElement->brands);
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'title',
            'products',
            'categories',
            'brands',
            'startDate',
            'endDate',
            'days',
            'iconWidth',
        ];
    }
}