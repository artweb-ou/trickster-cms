<?php

class brandsWidgetElement extends menuDependantStructureElement
{
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

    public function getElementData()
    {
        $data = [];

        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('languagesManager');
        /**
         * @var brandsListElement[] $brandsListElements
         */
        if ($brandsListElements = $structureManager->getElementsByType('brandsList', $languagesManager->getCurrentLanguageId())
        ) {
            foreach ($brandsListElements as &$brandsListElement) {
                /**
                 * @var brandElement[] $brands
                 */
                if ($brands = $brandsListElement->getBrandsList()) {
                    foreach ($brands as &$brand) {
                        if ($brand->originalName) {
                            $data[] = $brand->getElementData();
                        }
                    }
                }
            }
        }
        return $data;
    }
}