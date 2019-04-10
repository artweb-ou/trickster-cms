<?php

class deleteOrder extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setOrderStatus('deleted');
        $structureElement->persistElementData();

        $ordersElement = $structureManager->getElementByMarker('orders');
        $controller->redirect($ordersElement->URL);
    }
}