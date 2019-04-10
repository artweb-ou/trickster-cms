<?php

class receiveOpeningHoursGroup extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $linksManager = $this->getService('linksManager');
            $openingHoursInfoElements = $structureManager->getElementsByType('openingHoursInfo');
            foreach ($openingHoursInfoElements as $openingHoursInfoElement) {
                $linksManager->linkElements($openingHoursInfoElement->id, $structureElement->id
                    , 'openingHoursInfoGroup');
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hoursData',
        ];
    }
}


