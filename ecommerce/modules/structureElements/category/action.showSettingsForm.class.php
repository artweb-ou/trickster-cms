<?php

class showSettingsFormCategory extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param categoryElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            $publicRoot = $structureManager->getElementByMarker($marker);
            $languages = $structureManager->getElementsChildren($publicRoot->id);

            foreach ($languages as &$languageElement) {
                $selectedId = $structureElement->getValue('feedbackId', $languageElement->id);
                $structureElement->feedbackFormsList[$languageElement->id] = [];
                $elementsList = $structureManager->getElementsByType("feedback", $languageElement->id);
                foreach ($elementsList as $element) {
                    if ($element->structureType == 'feedback') {
                        $field = [];
                        $field['id'] = $element->id;
                        $field['title'] = $element->getTitle();
                        $field['select'] = $selectedId;

                        $structureElement->feedbackFormsList[$languageElement->id][] = $field;
                    }
                }
            }

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
            }

            $structureElement->getAdminProductsList();

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

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('action', 'receiveSettings');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('settings'));
        }
    }
}