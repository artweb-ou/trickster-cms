<?php

class importCalculationsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    public $defaultActionName = 'showMarginClasses';
    protected $allowedTypes = [
        'importCalculationsRule',
    ];
    public $role = 'container';
    protected $importPlugins;
    protected $marginClasses;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        // tmp
        $moduleStructure['pricingInput'] = 'array';
        $moduleStructure['marginClassesInput'] = 'array';
        $moduleStructure['newMarginClassInput'] = 'array';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getCategoryPluginIndex()
    {
        $index = [];
        $records = persistableCollection::getInstance('category_import_pricing')->conditionalLoad([
            'categoryId',
            'pluginId',
            'priceModifier',
        ], []);
        foreach ($records as $record) {
            $categoryId = $record['categoryId'];
            if (!isset($categoryId)) {
                $index[$categoryId] = [];
            }
            $index[$categoryId][$record['pluginId']] = $record['priceModifier'];
        }
        return $index;
    }

    public function getMarginClasses()
    {
        if ($this->marginClasses === null) {
            if ($records = $this->getMarginClassesRecords()) {
                foreach ($records as $record) {
                    $code = $record->fromPrice . '-' . $record->toPrice;
                    if (!isset($this->marginClasses[$code])) {
                        $this->marginClasses[$code] = [
                            'fromPrice' => $record->fromPrice,
                            'toPrice' => $record->toPrice,
                            'plugins' => [],
                        ];
                    }
                    $this->marginClasses[$code]['plugins'][$record->pluginId] = $record->priceModifier;
                }
            }
        }
        return $this->marginClasses;
    }

    public function getMarginClassesRecords()
    {
        $result = false;
        $usedPluginIds = [];
        if ($plugins = $this->getImportPlugins()) {
            foreach ($plugins as &$plugin) {
                $usedPluginIds[] = $plugin->id;
            }
        }
        $this->marginClasses = [];
        if ($usedPluginIds) {
            $collection = persistableCollection::getInstance('marginclass_import_pricing');
            if ($records = $collection->load(['pluginId' => $usedPluginIds], [
                'fromPrice' => 1,
                'toPrice' => 1,
            ])
            ) {
                $result = $records;
            }
        }
        return $result;
    }

    public function getImportPlugins()
    {
        if ($this->importPlugins === null) {
            $this->importPlugins = [];
            $structureManager = $this->getService('structureManager');
            $pluginsFolder = $structureManager->getElementByMarker('importPlugins');
            if ($pluginsFolder) {
                $this->importPlugins = $pluginsFolder->getChildrenList();
            }
        }
        return $this->importPlugins;
    }
}
