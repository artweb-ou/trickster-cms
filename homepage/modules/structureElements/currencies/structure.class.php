<?php

class currenciesElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['currency'];
    public $defaultActionName = 'showFullList';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function generateConfigs()
    {
        $structureManager = $this->getService('structureManager');
        $childrenList = $structureManager->getElementsChildren($this->id);

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
        $config = $this->getService('ConfigManager')->getConfig('currencies', true);
        $config->set('list', $currenciesData);
        $config->save();
    }
}