<?php

class currencySelectorElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'show';
    public $role = 'content';
    private $currenciesList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    public function getCurrenciesList()
    {
        if (is_null($this->currenciesList)) {
            $currencySelector = $this->getService(CurrencySelector::class);
            $this->currenciesList = $currencySelector->getCurrenciesList();
        }

        return $this->currenciesList;
    }
}


