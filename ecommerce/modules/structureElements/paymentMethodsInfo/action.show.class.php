<?php

class showPaymentMethodsInfo extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('show');
        if ($structureElement->final) {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->restart($parent->URL);
            }
        }
    }
}

