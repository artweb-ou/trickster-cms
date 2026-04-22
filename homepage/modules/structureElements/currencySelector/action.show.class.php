<?php

class showCurrencySelector extends structureElementAction
{
    /**
     * @param currencySelectorElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $controller = $this->getService(controller::class);
        if ($selectedCurrencyCode = $controller->getParameter('currency')) {
            $currencySelector = $this->getService(CurrencySelector::class);
            $currencySelector->setSelectedCurrencyCode($selectedCurrencyCode);
        }

        $structureElement->setTemplate('currencySelector.show.tpl');
    }
}

