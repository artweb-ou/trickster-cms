<?php

class receiveCategoriesImportCategories extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated && $structureElement->categoriesInput) {
            $selectedPlugin = null;
            $pluginParameter = $controller->getParameter('plugin');
            if ($pluginParameter) {
                $selectedPlugin = $structureManager->getElementById($pluginParameter);
            }
            if ($selectedPlugin) {
                $collection = persistableCollection::getInstance('module_category');
                $categoriesIds = [];
                $records = $collection->conditionalLoad('distinct(id)', [], [], [], [], true);
                foreach ($records as $record) {
                    $categoriesIds[] = $record['id'];
                }
                $importOrigin = $selectedPlugin->getOriginName();
                $collection = persistableCollection::getInstance('import_origin');
                $categoriesOriginEntries = $collection->load([
                    'importOrigin' => $importOrigin,
                    'elementId' => $categoriesIds,
                ], [], 'importId');

                foreach ($structureElement->categoriesInput as $categoryIdentifier => $categoryIdsInput) {
                    $categoryElementId = 0;
                    foreach ($categoryIdsInput as $categoryIdInput) {
                        if ($categoryIdInput) {
                            $categoryElementId = $categoryIdInput;
                            break;
                        }
                    }
                    $originEntry = isset($categoriesOriginEntries[$categoryIdentifier])
                        ? $categoriesOriginEntries[$categoryIdentifier]
                        : null;
                    if ($categoryElementId) {
                        if (!$originEntry) {
                            $originEntry = $collection->getEmptyObject();
                            $originEntry->importOrigin = $importOrigin;
                            $originEntry->importId = $categoryIdentifier;
                            $originEntry->elementId = $categoryElementId;
                            $originEntry->persist();
                            $categoriesOriginEntries[$categoryIdentifier] = $originEntry;
                        } elseif ($originEntry->elementId != $categoryElementId) {
                            $originEntry->elementId = $categoryElementId;
                            $originEntry->importId = $categoryIdentifier;
                            $originEntry->persist();
                        }
                    } elseif ($originEntry) {
                        $originEntry->delete();
                        unset($categoriesOriginEntries[$categoryIdentifier]);
                    }
                }
            }
            if ($pluginParameter) {
                $controller->redirect($structureElement->URL . 'plugin:' . $pluginParameter . '/');
            } else {
                $controller->redirect($structureElement->URL);
            }
        }
        $structureElement->executeAction("show");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'categoriesInput',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

