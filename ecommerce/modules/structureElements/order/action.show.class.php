<?php

class showOrder extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('show');
        $structureElement->getPaymentElement();
        $structureElement->recalculate();
    }
}

