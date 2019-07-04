<?php

class receiveCurrency extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;

            $maxDecimalsAmount = '3.00';
            if($structureElement->decimals > $maxDecimalsAmount) {
                $structureElement->decimals = $maxDecimalsAmount;
            }

            $structureElement->persistElementData();
            $parent = $structureManager->getElementsFirstParent($structureElement->id);
            if ($parent) {
                $parent->executeAction("generate");
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            "code",
            "symbol",
            "rate",
            "title",
            "decimals",
            "decPoint",
            "thousandsSep",
        ];
    }

    public function setValidators(&$validators)
    {
        $validators["title"][] = "notEmpty";
        $validators["code"][] = "notEmpty";
        $validators["symbol"][] = "notEmpty";
        $validators["rate"][] = "notEmpty";
    }
}