<?php

class changeStatusOrder extends structureElementAction
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
        $orderStatus = $controller->getParameter('orderStatus');
        if ($orderStatus !== 'payed' && $orderStatus !== 'sent') {
            return;
        }
        if ($orderStatus === 'payed') {
            $payment = $structureElement->getPaymentElement();
            $amountPaid = $payment->amount;
            $orderPrice = $structureElement->getTotalPrice();
            $partlyPaid = $amountPaid != $orderPrice;
            if ($partlyPaid) {
                $orderStatus = 'paid_partial';
            }
        }
        $structureElement->setOrderStatus($orderStatus);
        $structureElement->persistElementData();
        $structureElement->checkInvoiceSending();

        $renderer = $this->getService('renderer');
        $renderer->assign('responseStatus', 'success');
        $renderer->appendResponseData('order', $structureElement->getElementData());
    }
}
