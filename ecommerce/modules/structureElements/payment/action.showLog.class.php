<?php

class showLogPayment extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param paymentElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->executeAction('show');
        if ($structureElement->requested) {
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'payment.log.tpl');
            }
        }
    }
}
