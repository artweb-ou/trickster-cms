<?php

class receiveProductSelectionValue extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $words = explode(';', $structureElement->importKeywords);
            foreach ($words as &$word) {
                $word = trim(mb_strtolower($word));
            }
            $structureElement->importKeywords = implode(';', $words);

            $words = explode(';', $structureElement->excludeImportKeywords);
            foreach ($words as &$word) {
                $word = trim(mb_strtolower($word));
            }
            $structureElement->excludeImportKeywords = implode(';', $words);

            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();
            if ($structureElement->mergeIds) {
                foreach ($structureElement->mergeIds as &$valueIdToMerge) {
                    if (!$valueIdToMerge) {
                        continue;
                    }
                    $targetValueElement = $structureManager->getElementById($valueIdToMerge);
                    if ($targetValueElement) {
                        $targetValueRecords = $targetValueElement->getValueRecords();
                        if ($targetValueRecords) {
                            $selectionElementId = $structureElement->getSelectionElement()->id;
                            $productIdMap = array_flip($structureElement->getConnectedProductsIds());

                            foreach ($targetValueRecords as $record) {
                                if (isset($productIdMap[$record->productId])) {
                                    $record->delete();
                                } else {
                                    $record->parameterId = $selectionElementId;
                                    $record->value = $structureElement->id;
                                    $record->persist();
                                }
                            }
                        }
                        $collection = persistableCollection::getInstance('import_origin');
                        $targetOriginEntries = $collection->load(['elementId' => $targetValueElement->id]);
                        if ($targetOriginEntries) {
                            $originEntries = $collection->load(['elementId' => $structureElement->id]);
                            $originIndex = [];
                            foreach ($originEntries as &$entry) {
                                if (!isset($originIndex[$entry->importOrigin])) {
                                    $originIndex[$entry->importOrigin] = [];
                                }
                                $originIndex[$entry->importOrigin][$entry->importId] = true;
                            }
                            foreach ($targetOriginEntries as &$targetOriginEntry) {
                                if (isset($originIndex[$targetOriginEntry->importOrigin]) &&
                                    isset($originIndex[$targetOriginEntry->importId])
                                ) {
                                    $targetOriginEntry->delete();
                                } else {
                                    $targetOriginEntry->elementId = $structureElement->id;
                                    $targetOriginEntry->persist();
                                }
                            }
                        }
                        $targetValueElement->deleteElementData();
                    }
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hint',
            'importKeywords',
            'excludeImportKeywords',
            'price',
            'value',
            'image',
            'mergeIds',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}

