<?php

class generateCurrencies extends structureElementAction
{
    /**
     * @param currenciesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $childrenList = $structureManager->getElementsChildren($structureElement->id);

        $currenciesData = [];
        foreach ($childrenList as $element) {
            $currenciesDataItem = [];

            $currenciesDataItem['code'] = $element->code;
            $currenciesDataItem['rate'] = $element->rate;
            $currenciesDataItem['title'] = $element->title;
            $currenciesDataItem['symbol'] = $element->symbol;
            $currenciesDataItem['decimals'] = $element->decimals;
            $currenciesDataItem['decPoint'] = $element->decPoint;
            $currenciesDataItem['thousandsSep'] = $element->thousandsSep;

            $currenciesData[] = $currenciesDataItem;
        }
        $config = $this->getService(ConfigManager::class)->getConfig('currencies');
        $config->set('list', $currenciesData);
        $config->save();
    }
}

