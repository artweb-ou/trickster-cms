<?php

class showArticle extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param articleElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName($structureElement->getCurrentLayout('layout'));
        if ($structureElement->final) {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->restart($parent->URL);
            }
        }
    }
}

