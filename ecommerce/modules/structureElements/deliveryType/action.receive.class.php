<?php

class receiveDeliveryType extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->code == '') {
                $structureElement->code = $structureElement->title;
            }
            $structureElement->structureName = $structureElement->code;

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();

            $structureElement->linkWithElements($structureElement->paymentMethodsIds, "deliveryTypePaymentMethod");

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'code',
            'title',
            'calculationLogic',
            'image',
            'paymentMethodsIds',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['structureName'][] = 'notEmpty';
    }
}