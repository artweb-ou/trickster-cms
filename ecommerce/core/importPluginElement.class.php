<?php

abstract class importPluginElement extends structureElement implements specialFieldsElementInterface, importPluginElementInterface
{
    use specialFieldsElementTrait;
    public $dataResourceName = 'module_importplugin';
    public $defaultActionName = 'showForm';
    public $role = 'content';
    protected $categoriesTemplate = 'importPlugin.categories.tpl';
    protected $specialData;
    protected $connectedDeliveryTypes;
    protected $importIdElementIdIndex;
    protected $warehouse;
    protected $warehouseCategories;
    protected $priceCalculator;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['data'] = 'text';
        foreach ($this->getSpecialFields() as $fieldName => $specialField) {
            $moduleStructure[$fieldName] = $specialField['format'];
        }
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'data';
        foreach ($this->getSpecialFields() as $fieldName => $specialField) {
            if ($specialField['multiLanguage']) {
                $multiLanguageFields[] = $fieldName;
            }
        }
    }

    public function import($quick = false)
    {
        $warehouse = $this->getWarehouse();
        if (!$warehouse) {
            return;
        }
        $importCategoriesInfo = $this->getService('productsImportManager')
            ->getCategoriesInfoByOrigin($this->getOriginName());
        if ($importCategoriesInfo) {
            $importManager = $this->getService('productsImportManager');
            $syncronizer = new WarehouseSyncronizer($warehouse);
            $syncronizer->setImportCategoriesInfo($importCategoriesInfo);
            $syncronizer->setPriceCalculator($this->getPriceCalculator());
            $importManager->setImportOrigin($warehouse::CODE);
            $importManager->setImportLanguageCode(false);
            $syncronizer->setProductsImportManager($importManager);
            if ($quick) {
                $syncronizer->quickSync();
            } else {
                $syncronizer->fullSync();
            }
        }
    }

    public function getPriceCalculator()
    {
        if ($this->priceCalculator === null) {
            $this->priceCalculator = false;
            $importCategoriesInfo = $this->getService('productsImportManager')
                ->getCategoriesInfoByOrigin($this->getOriginName());
            if ($importCategoriesInfo) {
                $this->priceCalculator = new productPriceCalculator();
                if ($priceClassMargins = $this->getPriceClassMargins()) {
                    $this->priceCalculator->setPriceClassMargins($priceClassMargins);
                }
                if ($priceModifiers = $this->getCategoryPriceModifiers(array_keys($importCategoriesInfo))) {
                    $this->priceCalculator->setCategoriesPriceModifiers($priceModifiers);
                }
            }
        }
        return $this->priceCalculator;
    }

    public function getWarehouseCategories()
    {
        if ($this->warehouseCategories === null) {
            $this->warehouseCategories = [];
            if ($warehouse = $this->getWarehouse()) {
                $this->warehouseCategories = $warehouse instanceof WarehouseCategoriesTreeProvider
                    ? $warehouse->getCategoriesTree()
                    : $warehouse->getCategories();
            }
        }
        return $this->warehouseCategories;
    }

    public function getCategoryPriceModifiers(array $categoriesIds)
    {
        $priceModifiers = [];

        if ($categoriesIds) {
            $conditions = [
                [
                    'pluginId',
                    '=',
                    $this->id,
                ],
                [
                    'categoryId',
                    'IN',
                    $categoriesIds,
                ],
            ];
            $records = persistableCollection::getInstance('category_import_pricing')->conditionalLoad(
                ['categoryId', 'priceModifier'],
                $conditions
            );
            foreach ($records as $record) {
                $priceModifiers[$record['categoryId']] = (int)str_replace('%', '', $record['priceModifier']) / 100;
            }
        }
        return $priceModifiers;
    }

    public function getPriceClassMargins()
    {
        $conditions = [
            [
                'pluginId',
                '=',
                $this->id,
            ],
        ];
        $priceClassMargins = persistableCollection::getInstance('marginclass_import_pricing')->conditionalLoad(
            ['fromPrice', 'toPrice', 'priceModifier'],
            $conditions
        );
        foreach ($priceClassMargins as &$priceClassMargin) {
            if (stripos($priceClassMargin['priceModifier'], '%')) {
                $modifier = (int)str_replace('%', '', $priceClassMargin['priceModifier']) / 100;
                $priceClassMargin['priceModifier'] = $modifier;
            }
        }

        return $priceClassMargins;
    }

    public function getCategoriesTemplate()
    {
        return $this->categoriesTemplate;
    }

    public function getName()
    {
        return str_replace('ImportPluginElement', '', get_class($this));
    }

    public abstract function getOriginName();

    public function getCategoriesIdentifiers()
    {
        return [];
    }

    public function getImportIdCategoryIdIndex()
    {
        if ($this->importIdElementIdIndex === null) {
            $records = persistableCollection::getInstance('module_category')
                ->conditionalLoad('distinct(id)', [], [], [], [], true);
            if ($records) {
                $categoriesIds = [];
                foreach ($records as $record) {
                    $categoriesIds[] = $record['id'];
                }
                $this->importIdElementIdIndex = [];
                $collection = persistableCollection::getInstance('import_origin');
                $records = $collection->conditionalLoad(['elementId', 'importId'], [
                    [
                        'importOrigin',
                        '=',
                        $this->getOriginName(),
                    ],
                    [
                        'elementId',
                        'IN',
                        $categoriesIds,
                    ],
                ]);
                foreach ($records as $record) {
                    $this->importIdElementIdIndex[$record['importId']] = $record['elementId'];
                }
            }
        }
        return $this->importIdElementIdIndex;
    }

    public function getCategoryIdByImportId($importId)
    {
        $elementId = 0;
        $index = $this->getImportIdCategoryIdIndex();
        if ($index && !empty($index[$importId])) {
            $elementId = $index[$importId];
        }
        return $elementId;
    }
}