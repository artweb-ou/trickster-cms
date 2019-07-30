<?php

class shopCatalogueControlsElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    public function getCategories()
    {
        $result = [];
        if ($catalogue = $this->getCatalogue()) {
            $result = $catalogue->getInhabitedCategories();
        }
        return $result;
    }

    public function getShopIndexLetters()
    {
        $result = [];
        if ($catalogue = $this->getCatalogue()) {
            $result = $catalogue->getShopIndexLetters();
        }
        return $result;
    }

    public function getCatalogue()
    {
        static $result;

        if ($result === null) {
            $result = false;
            $structureManager = $this->getService('structureManager');
            $languageId = $this->getService('LanguagesManager')->getCurrentLanguageId();
            foreach ($structureManager->getElementsByType('shopCatalogue', $languageId) as $shopCatalogue) {
                $result = $shopCatalogue;
                break;
            }
        }
        return $result;
    }
}


