<?php

class WarehouseSyncronizer extends errorLogger
    implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $logOperations = false;
    protected $currentGroupId = 0;
    protected $vendorsIndex;
    protected $updatedProductStatuses = [];
    protected $disabledProductsIndex = [];
    /**@var productsImportManager */
    protected $productsImportManager;
    protected $importOrigin = '';
    protected $importCategoriesInfo = [];
    protected $syncStart = 0;
    protected $warehouseCategoriesIndex;
    /**@var Warehouse */
    protected $warehouse;
    /**@var productPriceCalculator */
    protected $priceCalculator;
    protected $logOpened = false;
    protected $logName = '';

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
        $this->importOrigin = $warehouse::CODE;
        $this->logName = $this->getService('PathsManager')->getPath('warehouseSyncLogs')
            . uniqid(date('Y-m-d-Hi_', time()) . $warehouse::CODE . '_') . '.log';
        $pathsManager = $this->getService('PathsManager');
        $tempPath = $pathsManager->getPath('temporary') . 'warehouse_' . $warehouse::CODE . '/';
        $pathsManager->ensureDirectory($tempPath);
        $warehouse->setTempDirectory($tempPath);
    }

    public function quickSync()
    {
        $this->logProgress("SYNC STARTED FROM {$this->importOrigin} (quick)");
        $this->prepareSync();

        if ($this->warehouse instanceof MinimumActiveProductsInfoProvider) {
            $productsIndex = $this->productsImportManager->getExistingProductsIndex($this->importOrigin);
            $existingProductsCodes = array_keys($productsIndex);
            $this->logProgress('Retrieving all products at once...');

            $products = $this->warehouse->getMinimumActiveProductsInfo($existingProductsCodes);
            if ($products !== null) {
                $linksManager = $this->getService('linksManager');
                $productsCount = count($products);
                $i = 1;
                $this->logProgress("Received $productsCount warehouse products");

                foreach ($products as $product) {
                    $this->logProgress("Updating product $i/$productsCount: {$product->title} (code: {$product->code})");
                    $productInfo = $productsIndex[$product->code];
                    $categoryIds = $linksManager->getConnectedIdList($productInfo['id'], 'catalogue', 'child');
                    $this->updateExistingProduct($product, reset($categoryIds));
                }
            } else {
                $this->logProgress('Failed retrieving products');
                $this->disabledProductsIndex = [];
            }
        } else {
            $structureManager = $this->getService('structureManager');
            foreach ($this->importCategoriesInfo as $categoryElementId => &$categoryCodes) {
                $categoryName = '';
                $element = $structureManager->getElementById($categoryElementId);
                if ($element) {
                    $categoryName = $element->title;
                }
                $this->logProgress('Processing category ' . $categoryElementId . ' (' . $categoryName . ')');
                $categoryStart = microtime(true);

                foreach ($categoryCodes as $categoryCode) {
                    $this->logProgress('Syncing warehouse category: ' . $categoryCode);

                    $start = microtime(true);
                    $products = $this->warehouse->getProductsByCategory($categoryCode);
                    if ($products !== null) {
                        $productsCount = count($products);
                        $i = 1;
                        $this->logProgress("Received $productsCount warehouse products");
                        foreach ($products as $product) {
                            $this->logProgress("Updating product $i/$productsCount: {$product->title} (code: {$product->code})");
                            $this->updateExistingProduct($product, $categoryElementId);
                        }
                        $end = microtime(true);
                        $this->logOperation($start, $end, memory_get_usage(), 'quickSync ' . $categoryCode);

                        $this->logProgress('Finished syncing warehouse category ' . $categoryCode
                            . '. Time: ' . ($end - $start));
                    } else {
                        $this->undisableCategoryProducts($categoryElementId);
                        $this->logProgress('Failed retrieving products');
                    }
                }
                $this->logProgress('Finished importing to category "' . $categoryName . '". Time: '
                    . (microtime(true) - $categoryStart));
            }
        }
        $this->finishSync();
    }

    public function fullSync()
    {
        $this->logProgress("SYNC STARTED FROM {$this->importOrigin}");
        $this->prepareSync();

        $this->logProgress('Retrieving warehouse categories');
        $this->warehouseCategoriesIndex = $this->warehouse->getCategories();
        $structureManager = $this->getService('structureManager');
        foreach ($this->importCategoriesInfo as $categoryElementId => &$categoryCodes) {
            $categoryName = '';
            $element = $structureManager->getElementById($categoryElementId);
            if ($element) {
                $categoryName = $element->title;
            }
            $this->logProgress('Importing to category ' . $categoryElementId . ' (' . $categoryName . ')');
            $categoryStart = microtime(true);

            foreach ($categoryCodes as $categoryCode) {
                $start = microtime(true);
                $this->currentGroupId = $categoryCode;
                $this->synchronizeCategoriesProducts($categoryElementId);
                $end = microtime(true);
                $this->logOperation($start, $end, memory_get_usage(), 'synchronizeCategoriesProducts ' . $categoryCode);
            }
            $this->logProgress('Finished importing to category "' . $categoryName . '". Time: '
                . (microtime(true) - $categoryStart));
        }
        $this->finishSync();
    }

    protected function prepareSync()
    {
        $this->logProgress('------------------------------------------------------------------------');
        $this->logProgress('Preparing... getting existing products info...');
        $this->syncStart = microtime(true);
        $productsIndex = $this->productsImportManager->getExistingProductsIndex($this->importOrigin);
        $existingProductsCodes = array_keys($productsIndex);
        $this->disabledProductsIndex = array_flip($existingProductsCodes);
    }

    protected function finishSync()
    {
        $this->logProgress('Finishing... Updating product statuses ('
            . count($this->updatedProductStatuses) . ')...');
        $this->productsImportManager->updateProductsStatuses($this->updatedProductStatuses);
        if (count($this->disabledProductsIndex)) {
            $this->logProgress('Finishing... Disabling outdated products (' .
                count($this->disabledProductsIndex) . ')...');
            $this->productsImportManager->disableProducts(array_keys($this->disabledProductsIndex));
        }
        $this->logProgress($this->importOrigin . ' SYNC FINISHED (in ' . (microtime(true) - $this->syncStart) . 's)');
    }

    protected function synchronizeCategoriesProducts($categoryElementId)
    {
        $this->logProgress('Syncing warhouse category ' . $this->currentGroupId);
        $start = microtime(true);

        $products = $this->warehouse->getProductsByCategory($this->currentGroupId);
        if ($products !== null) {
            $productsCount = count($products);
            $i = 1;
            $this->logProgress("Received $productsCount warehouse products");
            foreach ($products as &$product) {
                $this->logProgress("Importing product $i/$productsCount: {$product->title} (code: {$product->code})");
                $start = microtime(true);
                $this->importProduct($product);
                $this->productsImportManager->checkCategoryLink(
                    $product->code,
                    $categoryElementId,
                    $this->importOrigin
                );
                $this->updateExistingProduct($product, $categoryElementId);
                $this->saveProductImportCategoryInfo($product->code);
                $end = microtime(true);
                $this->logOperation(
                    $start,
                    $end,
                    memory_get_usage(),
                    'saveProductInfo ' . $this->currentGroupId . ': ' . $product->title
                );
            }
            $this->logProgress('Finished with warhouse category '
                . $this->currentGroupId . '(in '
                . (microtime(true) - $start) . 's)');
        } else {
            $this->logProgress('Failed retrieving products');
            $this->undisableCategoryProducts($categoryElementId);
        }
    }

    protected function saveProductImportCategoryInfo($productCode)
    {
        $warehouseCategory = isset($this->warehouseCategoriesIndex[$this->currentGroupId])
            ? $this->warehouseCategoriesIndex[$this->currentGroupId]
            : null;
        if ($warehouseCategory) {
            $productElementsIndex = $this->productsImportManager->getExistingProductsIndex($this->importOrigin);
            if (isset($productElementsIndex[$productCode])) {
                $productId = $productElementsIndex[$productCode]['id'];
                $this->productsImportManager->saveProductImportCategoryInfo(
                    $productId,
                    $this->importOrigin,
                    $warehouseCategory->code,
                    $warehouseCategory->title
                );
            }
        }
    }

    /*
     * Used to create & save a product that doesn't necessarily exist
     */
    protected function importProduct(WarehouseProduct $product)
    {
        $start = microtime(true);
        $existing = $this->productsImportManager->checkProductExistance($product->code, $this->importOrigin);
        $importInfo = [
            'title' => $product->title,
            'content' => $product->description,
            'code' => $product->manufacturerCode,
            'importId' => $product->code,
            'barcode' => $product->barcode,
            'warranty' => $product->warranty,
            'arrivalDate' => $product->dateExpected,
            'importOrigin' => $this->importOrigin,
            'availability' => 'quantity_dependent',
        ];
        if (!$existing) {
            $importInfo += [
                'inactive' => '1',
            ];
        }
        $imported = $this->productsImportManager->importProductInfo($importInfo, $this->importOrigin);
        if ($imported) {
            $end = microtime(true);
            $this->logOperation($start, $end, memory_get_usage(), 'importProductInfo : ' . $product->code);
            // parameters
            $parameters = $product->getParameters();
            $importInfo = [];
            foreach ($parameters as &$parameter) {
                if ($parameter->redundant) {
                    continue;
                }
                $importInfo[] = [
                    'importOrigin' => $this->importOrigin,
                    'importId' => $parameter->code,
                    'title' => $parameter->title,
                    'value' => $parameter->value,
                    'single' => '1',
                ];
            }
            if ($importInfo) {
                $start = microtime(true);
                $this->productsImportManager->importParameterInfo(
                    $importInfo,
                    $product->code,
                    $this->currentGroupId,
                    $this->importOrigin
                );
                $end = microtime(true);
                $this->logOperation(
                    $start,
                    $end,
                    memory_get_usage(),
                    'importParameterInfo : ' . $product->code
                );
            }
            // pics
            if (!$existing || !$this->productsImportManager->productHasImages($product->code, $this->importOrigin)) {
                $images = $product->getImages();
                if ($images) {
                    $start = microtime(true);
                    foreach ($images as &$resource) {
                        $this->productsImportManager->importImages(
                            (array)$resource->getLocalPath(),
                            $product->code,
                            $this->importOrigin
                        );
                        $resource->deleteLocalFile();
                    }
                    $end = microtime(true);
                    $this->logOperation($start, $end, memory_get_usage(), 'importImages : ' . $product->code);
                }
            }
            // brand
            if (!$existing || !$this->productsImportManager->productHasBrand($product->code, $this->importOrigin)) {
                $vendorName = $product->getVendorName();
                if ($vendorName && $product->vendorCode) {
                    $importInfo = [
                        'title' => $vendorName,
                        'importId' => $product->vendorCode,
                        'importOrigin' => $this->importOrigin,
                    ];

                    $start = microtime(true);
                    $this->productsImportManager->importBrandInfo($importInfo);
                    $this->productsImportManager->checkBrandLink(
                        $importInfo,
                        $product->code,
                        $this->importOrigin
                    );
                    $end = microtime(true);
                    $this->logOperation(
                        $start,
                        $end,
                        memory_get_usage(),
                        'importBrandInfo : ' . $product->code
                    );
                }
            }
        }
    }

    protected function updateExistingProduct(WarehouseProduct $warehouseProduct, $categoryId)
    {
        $productElementsIndex = $this->productsImportManager->getExistingProductsIndex($this->importOrigin);
        $importCode = $warehouseProduct->code;
        if (!isset($productElementsIndex[$importCode])) {
            return;
        }
        $existingProductInfo = $productElementsIndex[$importCode];
        $elementId = $existingProductInfo['id'];
        $newData = [];
        $availability = 'quantity_dependent';
        if ($existingProductInfo['availability'] !== $availability) {
            $newData['availability'] = $availability;
        }
        if ($existingProductInfo['quantity'] <> $warehouseProduct->quantity) {
            $newData['quantity'] = $warehouseProduct->quantity;
        }
        if (isset($existingProductInfo['importPrice'])) {
            $warehousePriceChanged = abs((float)$existingProductInfo['importPrice'] - (float)$warehouseProduct->price) > 0.01;
        }
        if ($warehousePriceChanged) {
            $newData['importPrice'] = $warehouseProduct->price;
        }
        if ($existingProductInfo['inactive'] == 1) {
            $newData['inactive'] = 0;
        }
        if (!(float)$existingProductInfo['directoPrice']) {
            $calculator = $this->priceCalculator;
            $calculator->setBasePrice($warehouseProduct->price);
            $calculator->setProductElementId($elementId);
            $calculator->setCategoryElementId($categoryId);
            $calculator->setImportOrigin($this->importOrigin);
            $calculator->setRecommendedPrice($warehouseProduct->rrp);
            $newPrice = $calculator->getSalePrice();
            $usingRecommendedPrice = $calculator->wasRecommendedPriceUsed();

            if (abs((float)$existingProductInfo['price'] - $newPrice) > 0.01) {
                $newData['price'] = $newPrice;
                $newData['vatIncluded'] = (int)$usingRecommendedPrice;
            }
        } else {
            // Directo price takes precedence, no need to calc/overwrite
        }
        if (count($newData) > 0) {
            $this->updatedProductStatuses[$elementId] = $newData;
        }
        unset($this->disabledProductsIndex[$importCode]);
    }

    // TODO: combine this and logProgress?
    protected function logOperation($start, $end, $memory, $text)
    {
        if ($this->logOperations) {
            $info = round($end - $start, 5) . ' ' . round($memory / (1024 * 1024), 2) . ' ' . $text . "\n";
            file_put_contents('import.txt', $info, FILE_APPEND);
        }
    }

    protected function undisableCategoryProducts($categoryElementId)
    {
        $linksManager = $this->getService('linksManager');
        $categoryProductsIds = $linksManager->getConnectedIdList($categoryElementId, 'catalogue', 'parent');
        if ($categoryProductsIds) {
            $importIds = $this->productsImportManager->elementsIdsToImportIds($categoryProductsIds, $this->importOrigin);
            if ($importIds) {
                $this->disabledProductsIndex = array_diff_key($this->disabledProductsIndex, array_flip($importIds));
            }
        }
    }

    public function setImportCategoriesInfo(array $importCategoriesInfo)
    {
        $this->importCategoriesInfo = $importCategoriesInfo;
    }

    public function setOperationLogging($enabled)
    {
        $this->logOperations = $enabled;
    }

    public function getUpdatedProductsIds()
    {
        return array_keys($this->updatedProductStatuses);
    }

    public function getDisabledProductsImportIds()
    {
        return array_keys($this->disabledProductsIndex);
    }

    /**
     * @param productPriceCalculator $priceCalculator
     */
    public function setPriceCalculator($priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    public function setProductsImportManager($productsImportManager)
    {
        $this->productsImportManager = $productsImportManager;
    }

    protected function logProgress($line)
    {
        static $initialized;
        static $eol = "\r\n";

        if ($initialized === null) {
            $pathsManager = $this->getService('PathsManager');
            $path = $pathsManager->getPath('warehouseSyncLogs');
            $logPathAvailable = $pathsManager->ensureDirectory($path);
            if (!$logPathAvailable) {
                $this->logError('Log path unavailable, progress logging disabled');
            } else {
                $this->logOpened = true;
            }
            $initialized = true;
        }
        if ($this->logOpened) {
            file_put_contents($this->logName, date('H:i:s'
                    , time()) . ' >> ' . $line . $eol, FILE_APPEND | LOCK_EX);
        }
    }
}
