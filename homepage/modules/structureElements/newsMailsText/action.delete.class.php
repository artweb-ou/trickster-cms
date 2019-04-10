<?php

class deleteNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $emailDispatcher = $this->getService('EmailDispatcher');
        $emailDispatcher->cancelReferencedDispatchments($structureElement->id);

        $structureElement->deleteElementData($structureElement->id);
        $parentElement = $structureManager->getElementsFirstParent($structureElement->id);
        $controller->restart($parentElement->URL);
    }
}


