<?php

class receiveCategory extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $destinationUrl = $structureElement->URL;

            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }

            $parent = $structureManager->getElementsFirstParent($structureElement->id);
            if ($categoriesFolder = $structureManager->getElementByMarker('categories')) {
                $linksManager = $this->getService('linksManager');
                $index = $linksManager->getElementsLinksIndex($structureElement->id, 'structure', 'child');
                $linkedToCategory = false;
                if ($structureElement->parentCategoriesIds) {
                    foreach ($structureElement->parentCategoriesIds as $parentId) {
                        if ($parentId != '' && $parentId != $structureElement->id) {
                            if (!isset($index[$parentId])) {
                                $linksManager->linkElements($parentId, $structureElement->id, 'structure');
                            } else {
                                unset($index[$parentId]);
                            }
                            $linkedToCategory = true;
                        }
                    }
                }
                if (!$linkedToCategory) {
                    $linksManager->linkElements($categoriesFolder->id, $structureElement->id, 'structure');
                }
                foreach ($index as &$linkToDelete) {
                    if ($linkToDelete->parentStructureId != $categoriesFolder->id || $linkedToCategory) {
                        $linksManager->unLinkElements($linkToDelete->parentStructureId, $structureElement->id, 'structure');
                    }
                }
            }

            //persist parameters
            $linksManager = $this->getService('linksManager');

            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'categoryParameter', 'parent');
            $parametersFolder = $structureManager->getElementByMarker('productparameters');
            $parametersGroups = $structureManager->getElementsChildren($parametersFolder->id);
            $parametersList = [];
            foreach ($parametersGroups as &$group) {
                $parametersList = array_merge($parametersList, $structureManager->getElementsChildren($group->id));
            }
            foreach ($parametersList as &$parameter) {
                if (isset($compiledLinks[$parameter->id]) && !in_array($parameter->id, $structureElement->parameters)) {
                    $compiledLinks[$parameter->id]->delete();
                } elseif (!isset($compiledLinks[$parameter->id]) && in_array($parameter->id, $structureElement->parameters)
                ) {
                    $linksManager->linkElements($structureElement->id, $parameter->id, 'categoryParameter');
                }
            }

            $structureElement->persistElementData();

            $controller->redirect($destinationUrl);
        }
        $structureElement->executeAction("showForm");
    }

    protected function connectWithProductCatalogues($structureElement)
    {
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        $connectedFoldersIdsIndex = array_flip($structureElement->getConnectedCatalogueFoldersIds());
        if ($productCatalogues = $structureManager->getElementsByType('productCatalogue')) {
            if (!$structureElement->getParentCategory()) {
                foreach ($productCatalogues as &$productCatalogue) {
                    if ($productCatalogue->categorized && $productCatalogue->connectAllCategories && $parentFolderElement = $productCatalogue->getContainerElement()
                    ) {
                        $linksManager->linkElements($parentFolderElement->id, $structureElement->id, 'catalogue');
                        if (isset($connectedFoldersIdsIndex[$parentFolderElement->id])) {
                            unset($connectedFoldersIdsIndex[$parentFolderElement->id]);
                        }
                    }
                }
            }
            if ($structureElement->productCataloguesIds) {
                foreach ($productCatalogues as &$productCatalogue) {
                    if ($productCatalogue->categorized && !$productCatalogue->connectAllCategories && $parentFolderElement = $productCatalogue->getContainerElement()
                    ) {
                        if (in_array($productCatalogue->id, $structureElement->productCataloguesIds) && $parentFolderElement
                        ) {
                            $linksManager->linkElements($parentFolderElement->id, $structureElement->id, 'catalogue');
                            if (isset($connectedFoldersIdsIndex[$parentFolderElement->id])) {
                                unset($connectedFoldersIdsIndex[$parentFolderElement->id]);
                            }
                        }
                    }
                }
            }
            //delete obsolete links
            foreach ($connectedFoldersIdsIndex as $connectedFolderId => &$value) {
                $linksManager->unLinkElements($connectedFolderId, $structureElement->id, 'catalogue');
            }
        }
    }


    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'hidden',
            'unit',
            'parameters',
            'productCataloguesIds',
            'feedbackId',
            'parentCategoriesIds',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}