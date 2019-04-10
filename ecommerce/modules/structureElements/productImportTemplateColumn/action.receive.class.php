<?php

class receiveProductImportTemplateColumn extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (($structureElement->productVariable != "parameter") || ($structureElement->productVariable == "parameter" && $structureElement->productParameterId)
            ) {
                if (!is_numeric($structureElement->columnNumber)) {
                    $structureElement->columnNumber = $structureElement->charToNum($structureElement->columnNumber);
                }

                $structureElement->persistElementData();
                if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                    $controller->redirect($parentElement->URL);
                }
            } else {
                $structureElement->setFormError("productParameterId");
            }
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'productVariable',
            'productParameterId',
            'columnNumber',
            'mandatory',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

