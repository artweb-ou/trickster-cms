<?php

class BankLogRecordInfo
{
    protected $id = 0;
    protected $paymentId;
    protected $fromBank = false;
    protected $details = [];
    protected $time = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = (int)$paymentId;
    }

    /**
     * @return boolean
     */
    public function isFromBank()
    {
        return $this->fromBank;
    }

    /**
     * @param boolean $fromBank
     */
    public function setFromBank($fromBank)
    {
        $this->fromBank = (bool)$fromBank;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param array $details
     */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = (int)$time;
    }
}
