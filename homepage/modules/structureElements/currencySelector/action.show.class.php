<?php

class showCurrencySelector extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $controller = $this->getService(controller::class);
        if ($selectedCurrencyCode = $controller->getParameter('currency')) {
            $currencySelector = $this->getService(CurrencySelector::class);
            $currencySelector->setSelectedCurrencyCode($selectedCurrencyCode);
        }

        $structureElement->setTemplate('currencySelector.show.tpl');
    }
}

