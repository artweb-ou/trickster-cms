<?php

class receiveMaksekeskusePaymentMethod extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->getId();
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->importExternalData(array());
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = array(
            'title',
            'description',
            'image',
            'deliveryTypesIds',
            'sendOrderConfirmation',
            'sendAdvancePaymentInvoice',
            'sendInvoice',
        );
    }

    public function setValidators(&$validators)
    {
    }
}

