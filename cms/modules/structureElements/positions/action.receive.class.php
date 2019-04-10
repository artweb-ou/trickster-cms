<?php

class receivePositions extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
        if ($currentElement = $structureManager->getCurrentElement()) {
            $structureManager->getElementsChildren($currentElement->id);

            $parentLinks = $linksManager->getElementsLinks($currentElement->id, '', 'parent');

            foreach ($parentLinks as $link) {
                if (isset($structureElement->positions[$link->childStructureId])) {
                    $link->position = $structureElement->positions[$link->childStructureId];
                    $link->persist();
                }
            }
        }

        $structureElement->setViewName('form');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['positions'];
    }
}
