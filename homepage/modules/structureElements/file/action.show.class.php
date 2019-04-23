<?php

class showFile extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->redirect($parent->URL, 301);
            }
        }
    }
}