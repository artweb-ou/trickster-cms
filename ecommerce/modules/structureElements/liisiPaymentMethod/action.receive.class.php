<?php

class receiveLiisiPaymentMethod extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if (!is_null($structureElement->getDataChunk("privateKeyFile")->originalName)) {
                $structureElement->privateKeyFile = "private_key_" . $structureElement->id;
                $structureElement->privateKeyFileName = $structureElement->getDataChunk("privateKeyFile")->originalName;
            }
            if (!is_null($structureElement->getDataChunk("bankCertificateFile")->originalName)) {
                $structureElement->bankCertificateFile = "bank_cert_" . $structureElement->id;
                $structureElement->bankCertificateFileName = $structureElement->getDataChunk("bankCertificateFile")->originalName;
            }
            $structureElement->importExternalData([]);
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'description',
            'image',
            'deliveryTypesIds',
            'sendOrderConfirmation',
            'sendAdvancePaymentInvoice',
            'sendInvoice',
            'sellerName',
            'sellerCode',
            'sellerAccount',
            'bankCertificateFile',
            'privateKeyFile',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}