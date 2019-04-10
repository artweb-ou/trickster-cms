<?php

class openingHoursElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        'openingHoursGroup',
    ];
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

    public function getShopsWithCustomOpeningHours()
    {
        $result = [];
        $structureManager = $this->getService('structureManager');
        $shopsElement = $structureManager->getElementByMarker('shops');
        if ($shopsElement) {
            foreach ($shopsElement->getChildrenList() as $shopElement) {
                if ($shopElement->customOpeningHours) {
                    $result[] = $shopElement;
                }
            }
        }
        return $result;
    }
}


