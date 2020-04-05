<?php

class receiveGenericIcon extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {

        if ($this->validated) {
            $structureElement->prepareActualData();
            $x = $structureElement->id;
            foreach ($structureElement->getMultilanguageDataChunk('image') as $languageId => $dataChunk) {
                if ($dataChunk->originalName) {
                    $structureElement->setValue('image', $structureElement->id . '_' . $languageId, $languageId);
                    $structureElement->setValue('originalName', $dataChunk->originalName, $languageId);
                }
            }

            $structureElement->persistElementData();
            /**
             * @var object $structureElement
             */
            $structureElement->updateConnectedProducts($structureElement->iconProducts);
            $structureElement->updateConnectedCategories($structureElement->iconCategories);
            $structureElement->updateConnectedBrands($structureElement->iconBrands);
            $structureElement->updateConnectedParameters($structureElement->iconProductParameters);

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'title',
            'iconProducts',
            'iconCategories',
            'iconBrands',
            'startDate',
            'endDate',
            'days',
            'iconWidth',
            'iconWidthOnProduct',
            'iconLocation',
            'iconBgColor',
            'iconTextColor',
            'iconRole',
            'applicableToAllProducts',
            'iconProductAvail',
            'iconProductParameters',
            'selectedIcons'
        ];
    }
}