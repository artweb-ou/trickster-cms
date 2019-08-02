<?php

class showSelectedEvents extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
                if ($fixedElement = $structureElement->getFixedElement()) {
                    if ($structureElement->title == '') {
                        $structureElement->title = $fixedElement->title;
                    }

                    $structureElement->URL = $fixedElement->URL;
                }
                $structureElement->setViewName($structureElement->getCurrentLayout());
                $structureElement->linkItems = $structureManager->getElementsChildren($structureElement->id);
        $structureElement->setTemplate('selectedEvents.content.tpl');
    }
}

