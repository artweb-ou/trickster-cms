<?php

class showNews extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
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

