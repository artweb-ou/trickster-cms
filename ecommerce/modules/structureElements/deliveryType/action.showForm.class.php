<?php

class showFormDeliveryType extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->paymentMethods = $structureElement->getAvailablePaymentMethods()) {
                $connectedIds = $structureElement->getConnectedPaymentMethodsIds();
                foreach ($structureElement->paymentMethods as $key => &$paymentMethod) {
                    if (in_array($paymentMethod['id'], $connectedIds)) {
                        $structureElement->paymentMethods[$key]['select'] = true;
                    } else {
                        $structureElement->paymentMethods[$key]['select'] = false;
                    }
                }
            }
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}