<?php

class receiveNewsMailsGroup extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            if ($addressesIdList = $structureElement->addAddresses) {
                foreach ($addressesIdList as &$addressId) {
                    if ($addressElement = $structureManager->getElementById($addressId)) {
                        $linksManager->linkElements($structureElement->id, $addressId, 'newsmailGroup');
                    }
                }
            }

            $controller->restart($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'marker',
            'addAddresses',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}

