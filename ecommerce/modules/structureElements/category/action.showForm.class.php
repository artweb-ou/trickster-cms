<?php

class showFormCategory extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');

        if ($structureElement->final) {
            $externalCategoryId = false;
            $linksManager = $this->getService('linksManager');

            if ($parametersFolder = $structureManager->getElementByMarker('productparameters')) {
                $valuesIds = array_flip($linksManager->getConnectedIdList($structureElement->id, 'categoryParameter', 'parent'));
                $structureElement->allParametersGroups = $structureManager->getElementsChildren($parametersFolder->id);
                foreach ($structureElement->allParametersGroups as &$group) {
                    if ($parametersList = $group->getParametersList()) {
                        foreach ($parametersList as &$parameter) {
                            if (isset($valuesIds[$parameter->id])) {
                                $parameter->selected2 = true;
                            } else {
                                $parameter->selected2 = false;
                            }
                        }
                    }
                }
                $structureElement->productCatalogues = [];
                $connectedFoldersIds = $structureElement->getConnectedCatalogueFoldersIds();
                $allCatalogues = $structureManager->getElementsByType('productCatalogue');
                if ($allCatalogues) {
                    foreach ($allCatalogues as &$catalogueElement) {
                        if ($catalogueElement->categorized && !$catalogueElement->connectAllCategories) {
                            if ($parentElement = $catalogueElement->getContainerElement()) {
                                $field = [];
                                $field['id'] = $catalogueElement->id;
                                $field['title'] = $catalogueElement->getTitle();
                                $field['select'] = in_array($parentElement->id, $connectedFoldersIds);

                                $structureElement->productCatalogues[] = $field;
                            }
                        }
                    }
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}