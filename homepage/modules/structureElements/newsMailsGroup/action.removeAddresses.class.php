<?php

class removeAddressesNewsMailsGroup extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailsGroupElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $linksManager = $this->getService(linksManager::class);
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