<?php

class generateCurrencies extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $childrenList = $structureManager->getElementsChildren($structureElement->id);

        $currenciesData = [];
        foreach ($childrenList as &$element) {
            $currenciesDataItem = [];

            $currenciesDataItem['code'] = $element->code;
            $currenciesDataItem['rate'] = $element->rate;
            $currenciesDataItem['title'] = $element->title;
            $currenciesDataItem['symbol'] = $element->symbol;

            $currenciesData[] = $currenciesDataItem;
        }
        $config = $this->getService('ConfigManager')->getConfig('currencies');
        $config->set('list', $currenciesData);
        $config->save();
    }
}

