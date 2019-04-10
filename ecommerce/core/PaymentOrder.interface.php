<?php

interface PaymentOrderInterface
{
    public function setOrderStatus($orderStatus);

    public function getOrderStatus();

    public function getTotalPrice();

    public function getInvoiceNumber($type = 'invoice');
}