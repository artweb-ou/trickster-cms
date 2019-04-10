<?php

class showFullListPayments extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->paymentsList = $structureElement->getPaymentsPage();
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'payments.list.tpl');
        }
    }
}


