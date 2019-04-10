<?php

class showCurrencySelector extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $controller = $this->getService('controller');
        if ($selectedCurrencyCode = $controller->getParameter('currency')) {
            $currencySelector = $this->getService('CurrencySelector');
            $currencySelector->setSelectedCurrencyCode($selectedCurrencyCode);
        }

        $structureElement->setTemplate('currencySelector.show.tpl');
    }
}

