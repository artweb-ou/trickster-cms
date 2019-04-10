<?php

interface TransactionResponsePaymentMethodInterface
{
    public function getTransactionResponse($processingResult);
}