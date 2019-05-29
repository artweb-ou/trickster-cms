<?php

interface PaymentOrderInterface
{
    public function setOrderStatus($newOrderStatus);

    public function getOrderStatus();

    public function getTotalPrice();

    public function getInvoiceNumber($type = 'invoice');
}