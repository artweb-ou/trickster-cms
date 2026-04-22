<?php

class showNews extends structureElementAction
{
    /**
     * @param newsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        // TODO: why are assigning parentMenuElement? Remove if unneeded or explain a comment
        $structureElement->parentMenuElement = $structureManager->getElementsFirstParent($structureElement->id);
        if ($structureElement->requested) {
            $structureElement->setViewName('details');
        } else {
            $structureElement->setViewName('short');
        }
    }
}

