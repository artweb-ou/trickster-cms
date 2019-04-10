<?php

class receiveNewsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $numberFields = [
                'displayAmount',
                'archiveEnabled',
                'itemsOnPage',
            ];
            $structureElement->structureName = $structureElement->title;
            foreach ($numberFields as &$numberField) {
                $structureElement->$numberField = (int)$structureElement->$numberField;
            }

            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'displayAmount',
            'archiveEnabled',
            'itemsOnPage',
            'columns',
            'hidden',
        ];
    }
}


