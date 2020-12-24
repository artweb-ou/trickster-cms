<?php

class receiveMarginClassesImportCalculations extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated && ($structureElement->marginClassesInput || $structureElement->newMarginClassInput)) {
            $records = $structureElement->getMarginClassesRecords();
            foreach ($structureElement->marginClassesInput as $priceClass => $pluginsInfo) {
                if ($strings = explode('-', $priceClass)) {
                    $fromPrice = floatval($strings[0]);
                    $toPrice = floatval($strings[1]);
                    foreach ($pluginsInfo as $pluginId => $priceModifier) {
                        if ($priceModifier != '') {
                            $recordUpdate = false;
                            foreach ($records as $recordKey => $record) {
                                if ($record->fromPrice == $fromPrice && $record->toPrice == $toPrice && $record->pluginId == $pluginId) {
                                    $record->priceModifier = $priceModifier;
                                    $record->persist();
                                    unset($records[$recordKey]);
                                    $recordUpdate = true;
                                }
                            }
                            if (!$recordUpdate) {
                                $newRecord = persistableCollection::getInstance('marginclass_import_pricing')
                                    ->getEmptyObject();
                                $newRecord->fromPrice = $fromPrice;
                                $newRecord->toPrice = $toPrice;
                                $newRecord->pluginId = $pluginId;
                                $newRecord->priceModifier = $priceModifier;
                                $newRecord->persist();
                            }
                        }
                    }
                }
            }
            foreach ($records as $recordToDelete) {
                $recordToDelete->delete();
            }

            $fromPrice = 0;
            $toPrice = 0;
            if ($structureElement->newMarginClassInput['fromPrice'] != '') {
                $fromPrice = floatval($structureElement->newMarginClassInput['fromPrice']);
            }
            if ($structureElement->newMarginClassInput['toPrice'] != '') {
                $toPrice = floatval($structureElement->newMarginClassInput['toPrice']);
            }
            if ($fromPrice < $toPrice) {
                if (isset($structureElement->newMarginClassInput['plugins'])) {
                    foreach ($structureElement->newMarginClassInput['plugins'] as $pluginId => $priceModifier) {
                        $newRecord = persistableCollection::getInstance('marginclass_import_pricing')->getEmptyObject();
                        $newRecord->fromPrice = $fromPrice;
                        $newRecord->toPrice = $toPrice;
                        $newRecord->pluginId = $pluginId;
                        $newRecord->priceModifier = $priceModifier;
                        $newRecord->persist();
                    }
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("show");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'newMarginClassInput',
            'marginClassesInput',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

