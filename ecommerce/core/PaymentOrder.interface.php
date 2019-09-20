<?php

interface PaymentOrderInterface
{
    public function setOrderStatus($newOrderStatus);

    public function getOrderStatus();

    public function getTotalPrice($formatted = false);

    public function getInvoiceNumber($type = 'invoice');
}