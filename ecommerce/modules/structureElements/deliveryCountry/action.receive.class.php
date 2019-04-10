<?php

class receiveDeliveryCountry extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['structureName', 'title', 'iso3166_1a2', 'conditionsText'];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
        $validators['iso3166_1a2'][] = 'notEmpty';
    }
}

