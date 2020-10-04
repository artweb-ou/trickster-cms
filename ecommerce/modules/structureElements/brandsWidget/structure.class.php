<?php

class brandsWidgetElement extends menuDependantStructureElement
{
    use JsonDataProviderElement;
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    public function getTemplate($viewName = null)
    {
        if (!is_null($viewName)) {
            if ($viewName == "footer") {
                $viewName = "widget";
            }
            return $this->structureType . '.' . $viewName . '.tpl';
        }

        if (is_null($this->template)) {
            $this->template = $this->structureType . '.' . $this->viewName . '.tpl';
        }
        return $this->template;
    }

    public function getBrandsData()
    {
        $data = [];
        if ($brands = $this->getBrands()) {
            foreach ($brands as $brand) {
                if ($brand->originalName) {
                    $data[] = $brand->getElementData('api');
                }
            }
        }

        return $data;
    }

    /**
     * @return brandElement[]
     */
    public function getBrands()
    {
        $brands = [];
        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('LanguagesManager');
        /**
         * @var brandsListElement[] $brandsListElements
         */
        if ($brandsListElements = $structureManager->getElementsByType('brandsList', $languagesManager->getCurrentLanguageId())
        ) {
            foreach ($brandsListElements as &$brandsListElement) {
                if ($brandsListBrands = $brandsListElement->getBrandsList()) {
                    array_merge($brands, $brandsListBrands);
                }
            }
        }
        return $brands;
    }
}