<?php

class receiveProductSelection extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            if ($structureElement->getDataChunk('image')->originalName !== null) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk('image')->originalName;
            }
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $linksManager = $this->getService('linksManager');
            if ($connectedIds = $structureElement->getConnectedCategoriesIds()) {
                foreach ($connectedIds as $connectedId) {
                    if (!in_array($connectedId, $structureElement->categoriesIds)) {
                        $linksManager->unLinkElements($connectedId, $structureElement->id, 'categoryParameter');
                    }
                }
            }
            foreach ($structureElement->categoriesIds as $idToConnect) {
                if (!in_array($idToConnect, $connectedIds)) {
                    $linksManager->linkElements($idToConnect, $structureElement->id, 'categoryParameter');
                }
            }

            if ($structureElement->mergeId) {
                $targetElement = $structureManager->getElementById($structureElement->mergeId);
                if ($targetElement) {
                    $collection = persistableCollection::getInstance('import_origin');
                    $originEntries = $collection->load(['elementId' => $targetElement->id]);

                    if ($targetElement->structureType == 'productSelection') {
                        $children = $targetElement->getChildrenList();
                        foreach ($children as &$child) {
                            $linksManager->unLinkElements($targetElement->id, $child->id);
                            $linksManager->linkElements($structureElement->id, $child->id);
                        }
                        $collection = persistableCollection::getInstance('module_product_parameter_value');
                        $collection->updateData(
                            ['parameterId' => $structureElement->id],
                            [
                                'parameterId' => $targetElement->id,
                            ]
                        );
                        $targetElement->childrenList = [];
                    } else {
                        $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
                        $records = $valuesCollection->load(
                            [
                                'parameterId' => $targetElement->id,
                            ]
                        );
                        $productsRecordsIndex = [];
                        foreach ($records as &$record) {
                            if (!isset($productsRecordsIndex[$record->productId])) {
                                $productsRecordsIndex[$record->productId] = [];
                            }
                            $productsRecordsIndex[$record->productId][$record->languageId] = $record;
                        }

                        $languagesIds = $this->getService('languagesManager')->getLanguagesIdList();
                        $firstLanguageId = reset($languagesIds);

                        $elementsIndex = [];

                        foreach ($productsRecordsIndex as $productId => &$productLanguageRecords) {
                            $valueInfo = [];
                            foreach ($languagesIds as &$languageId) {
                                if (!isset($productLanguageRecords[$languageId])) {
                                    $title = reset($productLanguageRecords)->value;
                                } else {
                                    $title = $productLanguageRecords[$languageId]->value;
                                }
                                $valueInfo[$languageId]['title'] = $title;
                            }
                            $parameterIdentifier = mb_strtolower(trim($valueInfo[$firstLanguageId]['title']));

                            $productsImportManager = $this->getService('productsImportManager');

                            if (!isset($elementsIndex[$parameterIdentifier])) {
                                $element = $structureManager->createElement(
                                    'productSelectionValue',
                                    'show',
                                    $structureElement->id
                                );
                                $element->prepareActualData();
                                $element->persistElementData();

                                $element->importExternalData($valueInfo, (array)'title');
                                $element->persistElementData();
                                $elementsIndex[$parameterIdentifier] = $element;
                                foreach ($originEntries as &$entry) {
                                    $productsImportManager->recordElementImport(
                                        $element->id,
                                        $parameterIdentifier,
                                        $entry->importOrigin
                                    );
                                }
                            } else {
                                $element = $elementsIndex[$parameterIdentifier];
                            }
                            $newValue = $valuesCollection->getEmptyObject();
                            $newValue->parameterId = $structureElement->id;
                            $newValue->productId = $productId;
                            $newValue->value = $element->id;
                            $newValue->persist();
                        }
                        foreach ($records as &$record) {
                            $record->delete();
                        }
                    }
                    foreach ($originEntries as &$originEntry) {
                        $originEntry->elementId = $structureElement->id;
                        $originEntry->persist();
                    }
                    $targetElement->deleteElementData();
                }
            }
            // check category links
            $linksManager = $this->getService('linksManager');
            if ($connectedCategoryIds = $structureElement->getConnectedFilterableCategoriesIds()) {
                foreach ($connectedCategoryIds as &$connectedCategoryId) {
                    if (!in_array($connectedCategoryId, $structureElement->formFilterableCategoriesIds)) {
                        $linksManager->unLinkElements($connectedCategoryId, $structureElement->id, 'productSelectionFilterableCategory');
                    }
                }
            }
            foreach ($structureElement->formFilterableCategoriesIds as $selectedCategoryId) {
                $linksManager->linkElements($selectedCategoryId, $structureElement->id, 'productSelectionFilterableCategory');
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'primary',
            'code',
            'type',
            'option',
            'categoriesIds',
            'hint',
            'mergeId',
            'formFilterableCategoriesIds',
            'controlType',
            'influential',
            'image',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


