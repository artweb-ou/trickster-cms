<?php

class receiveCalculationsImportCalculations extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated && $structureElement->pricingInput) {
            $collection = persistableCollection::getInstance('category_import_pricing');
            $previousEntries = $collection->load([]);
            $previousEntriesIndex = [];
            foreach ($previousEntries as &$entry) {
                $previousEntriesIndex[$entry->categoryId . '_' . $entry->pluginId] = $entry;
            }
            foreach ($structureElement->pricingInput as $categoryId => &$pluginsInfo) {
                foreach ($pluginsInfo as $pluginId => &$priceModifier) {
                    if (!$priceModifier) {
                        continue;
                    }
                    $entryKey = $categoryId . '_' . $pluginId;
                    if (isset($previousEntriesIndex[$entryKey])) {
                        $newEntry = $previousEntriesIndex[$entryKey];
                        unset($previousEntriesIndex[$entryKey]);
                        if ($newEntry->priceModifier != $priceModifier) {
                            $newEntry->priceModifier = $priceModifier;
                            $newEntry->persist();
                        }
                    } else {
                        $newEntry = $collection->getEmptyObject();
                        $newEntry->priceModifier = $priceModifier;
                        $newEntry->categoryId = $categoryId;
                        $newEntry->pluginId = $pluginId;
                        $newEntry->persist();
                    }
                }
            }
            foreach ($previousEntriesIndex as &$oldEntry) {
                $collection->deleteObject($oldEntry);
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("show");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'pricingInput',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

