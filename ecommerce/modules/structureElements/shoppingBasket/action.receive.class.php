<?php

class receiveShoppingBasket extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->title != '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'destination',
            'productAddedText',
            'paymentInvoiceText',
            'paymentQueryText',
            'paymentFailedText',
            'paymentSuccessfulText',
            'columns',
            'hidden',
            'conditionsLink',
        ];
    }
}