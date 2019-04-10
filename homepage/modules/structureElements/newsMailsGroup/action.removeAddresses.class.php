<?php

class removeAddressesNewsMailsGroup extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
        if ($this->validated) {
            $elements = $structureElement->elements;
            foreach ($elements as $elementId => &$value) {
                $linksManager->unlinkElements($structureElement->id, $elementId, 'newsmailGroup');
            }
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['elements'];
    }
}