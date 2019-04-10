<?php

class receiveOpeningHoursInfo extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $linksManager = $this->getService('linksManager');
            $openingHoursGroupElements = $structureManager->getElementsByType('openingHoursGroup');
            foreach ($openingHoursGroupElements as $openingHoursGroupElement) {
                $linksManager->linkElements($structureElement->id, $openingHoursGroupElement->id
                    , 'openingHoursInfoGroup');
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'exceptional',
            'displayMenus',
        ];
    }
}