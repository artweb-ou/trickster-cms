<?php

class deleteNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailsTextElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $emailDispatcher = $this->getService(EmailDispatcher::class);
        $emailDispatcher->cancelReferencedDispatchments($structureElement->id);

        $structureElement->deleteElementData($structureElement->id);
        $parentElement = $structureManager->getElementsFirstParent($structureElement->id);
        $controller->restart($parentElement->URL);
    }
}


