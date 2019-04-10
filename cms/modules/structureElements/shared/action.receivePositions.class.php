<?php

class receivePositionsShared extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->positionsForm = $structureManager->createElement('positions', 'receive', $structureElement->id)
        ) {
            $structureElement->setViewName('positions');
            $controller->redirect($structureElement->getFormActionURL() . 'id:' . $structureElement->id . '/action:showPositions/');
        }
    }
}