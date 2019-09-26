<?php

class payShoppingBasket extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param shoppingBasketElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $user = $this->getService('user');
        $linksManager = $this->getService('linksManager');
        $structureElement->shoppingBasket = $this->getService('shoppingBasket');

        if ($structureElement->shoppingBasket->getProductsList()) {
            $selectedMethodId = (int)$structureElement->paymentMethodId;
            if (!$selectedMethodId) {
                if ($selectedMethodId = (int)$controller->getParameter('bank')) {
                    $this->logError('Deprecated parameter "bank" used');
                }
            }
            if ($selectedMethodId) {
                // create a new order
                /**
                 * @var orderElement $orderElement
                 */
                $orderElement = $structureManager->createElement('order', 'create', $structureElement->id);
                $orderElement->recalculate();
                $orderElement->logVisitorEvent('successful_order');
                $ordersElementId = $structureManager->getElementIdByMarker("orders");
                $structureManager->moveElement($structureElement->id, $ordersElementId, $orderElement->id);
                $linksManager->linkElements($user->id, $orderElement->id, 'userOrder');

                // Subscribe buyer to newsletter
                $payerEmail = $orderElement->payerEmail;
                if ($newsMailSubscription = $this->getService('NewsMailSubscription')) {
                    $newsMailSubscription->subscribeEmailToNewsMailGroup($payerEmail);
                }
                $visitorsManager = $this->getService(VisitorsManager::class);
                $visitorsManager->saveCurrentVisitorOrder($orderElement);

                // create a new payment record
                /**
                 * @var paymentElement $paymentElement
                 */
                $paymentElement = $structureManager->createElement('payment', 'show', $structureElement->id);
                $paymentElement->prepareActualData();
                $paymentElement->userId = $user->id;

                /**
                 * @var paymentMethodElement $selectedMethodElement
                 */
                $selectedMethodElement = null;
                $availablePaymentMethods = $structureElement->getAvailablePaymentMethods();
                foreach ($availablePaymentMethods as &$paymentMethod) {
                    if ($paymentMethod->id == $selectedMethodId) {
                        $selectedMethodElement = $paymentMethod;
                        break;
                    }
                }
                if ($selectedMethodElement) {
                    $methodName = strtolower($selectedMethodElement->getName());

                    $paymentElement->bank = $methodName;
                    $paymentElement->methodId = $selectedMethodId;
                    $paymentElement->orderId = $orderElement->id;
                    $paymentElement->description = $orderElement->getInvoiceNumber();
                    $paymentElement->amount = $orderElement->getTotalPrice();
                    $paymentElement->paymentStatus = 'undefined';
                    $paymentElement->persistElementData();

                    if ($selectedMethodElement->sendOrderConfirmation) {
                        $orderElement->sendOrderEmail('orderConfirmation');
                    }
                    if ($selectedMethodElement->sendAdvancePaymentInvoice) {
                        $orderElement->sendOrderEmail('advancePaymentInvoice');
                    }

                    $paymentsElementId = $structureManager->getElementIdByMarker("payments");
                    $structureManager->moveElement($structureElement->id, $paymentsElementId, $paymentElement->id);
                    $linksManager->linkElements($orderElement->id, $paymentElement->id, 'orderPayment');

                    $banklinkResultURL = $structureElement->URL . 'id:' . $structureElement->id . '/action:paymentResult/order:' . $orderElement->id . '/';
                    // make the actual payment
                    if ($methodName == 'invoice' || $methodName == 'query') {
                        $paymentElement->executeAction('receive');
                        $controller->redirect($banklinkResultURL);
                    } else {
                        $user->setStorageAttribute('banklinkResultURL', $banklinkResultURL);
                        $paymentElement->executeAction('send');
                    }
                } else {
                    $controller->redirect($structureElement->URL);
                }
            } else {
                $structureElement->setViewName('checkout');
                $structureElement->setFormError('paymentMethodId');
            }
        } else {
            // session may have expired
            $controller->redirect($structureElement->URL);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'paymentMethodId',
        ];
    }
}

