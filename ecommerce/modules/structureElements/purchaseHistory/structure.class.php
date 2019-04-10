<?php

class purchaseHistoryElement extends structureElement
{
    use AutoMarkerTrait;
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $ordersList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    public function getOrdersList()
    {
        if (!is_null($this->ordersList)) {
            return $this->ordersList;
        }
        $this->ordersList = [];
        $structureManager = $this->getService('structureManager');
        $user = $this->getService('user');
        $linksManager = $this->getService('linksManager');

        if ($user->userName != 'anonymous') {
            $connectedOrdersIds = $linksManager->getConnectedIdList($user->id, 'userOrder', 'parent');
            if ($orders = $structureManager->getElementsByIdList($connectedOrdersIds, $this->id)) {
                $ordersTitles = [];
                foreach ($orders as &$order) {
                    if ($payment = $order->getPaymentElement()) {
                        $order->paidAmount = $payment->amount;
                    } else {
                        $order->paidAmount = 0;
                    }
                    $ordersTitles[] = strtotime($order->dateCreated);
                }
                array_multisort($ordersTitles, SORT_DESC, $orders);
                $this->ordersList = $orders;
            }
        }
        return $this->ordersList;
    }
}
