<?php

class showDeliveryProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setFormValue('formDeliveries', $structureElement->getFormDeliveries());
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('action', 'receiveDelivery');
            $renderer->assign('form', $structureElement->getForm('delivery'));
            $renderer->assign('connectedDiscounts', $structureElement->getAllConnectedDiscounts());
        }
    }
}

