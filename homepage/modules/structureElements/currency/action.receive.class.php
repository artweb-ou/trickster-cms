<?php

class receiveCurrency extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param currencyElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->structureName = $structureElement->title;

            $maxDecimalsAmount = 3;
            if ($structureElement->decimals > $maxDecimalsAmount) {
                $structureElement->decimals = $maxDecimalsAmount;
            }

            $structureElement->persistElementData();
            /**
             * @var currenciesElement $parent
             */
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $parent->generateConfigs();
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