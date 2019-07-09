<?php

/**
 * Class paymentElement
 *
 * @property string description
 * @property int orderId
 * @property int userId
 * @property string paymentStatus
 * @property string payer
 * @property string account
 * @property string date
 * @property float amount
 * @property string bank
 * @property int methodId
 * @property string currency
 */
class paymentElement extends structureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_payment';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    /**
     * @var PaymentOrderInterface
     */
    protected $orderElement;
    public $userElement;
    public $username;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['description'] = 'text';
        $moduleStructure['orderId'] = 'text';
        $moduleStructure['userId'] = 'text';
        $moduleStructure['paymentStatus'] = 'text';
        $moduleStructure['payer'] = 'text';
        $moduleStructure['account'] = 'text';
        $moduleStructure['date'] = 'dateTime';
        $moduleStructure['amount'] = 'money';
        $moduleStructure['bank'] = 'text';
        $moduleStructure['methodId'] = 'text';
        $moduleStructure['currency'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLog',
        ];
    }

    /**
     * @return bool|PaymentOrderInterface
     */
    public function getOrderElement()
    {
        if ($this->orderElement === null) {
            $this->orderElement = false;
            $structureManager = $this->getService('structureManager');
            $orderElement = false;
            if ($this->orderId) {
                $orderElement = $structureManager->getElementById($this->orderId);
            }
            //workaround for public?
            if (!$orderElement) {
                $orderElement = $structureManager->getElementById($this->orderId, $this->id, true);
            }
            if ($orderElement instanceof PaymentOrderInterface) {
                $this->orderElement = $orderElement;
            }
        }

        return $this->orderElement;
    }

    public function getStatusText()
    {
        $translationsManager = $this->getService('translationsManager');
        return $translationsManager->getTranslationByName('payment.payment_' . $this->paymentStatus, 'adminTranslations');
    }

    public function getUserElement()
    {
        if (is_null($this->userElement)) {
            $this->userElement = false;
            $structureManager = $this->getService('structureManager');

            if ($userElement = $structureManager->getElementById($this->userId)) {
                $this->userElement = $userElement;
            }
        }
        return $this->userElement;
    }

    public function getPaymentMethodElement()
    {
        $paymentMethodElement = false;
        if ($this->methodId) {
            $paymentMethodElement = $this->getService('structureManager')->getElementById($this->methodId, $this->id, true);
        }
        return $paymentMethodElement;
    }

    public function approve()
    {
        if ($this->paymentStatus != 'success') {
            $this->paymentStatus = 'success';
        }
        if (!$this->date) {
            $this->date = date('d.m.Y', time());
        }
        $this->persistElementData();
    }

    public function getRecords()
    {
        return $this->getService('bankLog')->getRecords($this->id);
    }

    public function updateOrderStatus()
    {
        if ($orderElement = $this->getOrderElement()) {
            if ($this->paymentStatus == 'success') {
                $amountPaid = $this->amount;
                $orderPrice = $orderElement->getTotalPrice(false);
                $partlyPaid = $amountPaid != $orderPrice;
                if ($partlyPaid) {
                    $orderElement->setOrderStatus('paid_partial');
                } else {
                    $orderElement->setOrderStatus('payed');
                }
            } elseif ($this->paymentStatus == 'deferred' || $this->bank == 'invoice'
                || $this->bank == 'query'
            ) {
                $orderElement->setOrderStatus('undefined');
            } else {
                $orderElement->setOrderStatus('failed');
            }
        }
    }

    public function getAmount($formatted = true)
    {
        return $this->getValue('amount');
    }
}