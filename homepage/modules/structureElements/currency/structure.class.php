<?php

/**
 * Class currencyElement
 *
 * @property int $decimals
 */
class currencyElement extends structureElement
{
    public $dataResourceName = 'module_currency';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['code'] = 'text';
        $moduleStructure['rate'] = 'floatNumber';
        $moduleStructure['title'] = 'text';
        $moduleStructure['symbol'] = 'text';
        $moduleStructure['decimals'] = 'text';
        $moduleStructure['decPoint'] = 'text';
        $moduleStructure['thousandsSep'] = 'text';
    }
}