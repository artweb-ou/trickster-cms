<?php

class showFormPayment extends structureElementAction
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
            if ($orderElement = $structureElement->getOrderElement()) {
                if ($structureElement->orderId == '') {
                    $structureElement->orderId = $orderElement->id;
                }
                if ($structureElement->userId == '') {
                    $structureElement->userId = $orderElement->userId;
                }
            }

            if ($userElement = $structureElement->getUserElement()) {
                $structureElement->user = $userElement;
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('action', 'persist');
                $renderer->assign('orderElement', $structureElement->getOrderElement());
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}