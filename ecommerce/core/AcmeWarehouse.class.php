<?php

class AcmeWarehouse extends Warehouse
    implements WarehouseCategoriesTreeProvider
{
    const CODE = 'acc';
    protected $language = 'en-us';
    protected $currency = 'EUR';
    protected $client;
    protected $vendors;
    protected $categories;
    protected $categoriesTree;
    protected $barcodes;

    public function __construct($licenseKey = ACC_LICENSE_KEY)
    {
        $this->client = new SoapClient('https://api.acme.lt/1.0/commerce.asmx?WSDL', [
            'trace' => true,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'keep_alive' => false,
        ]);
        $header = new SoapHeader('http://schemas.acme.eu/', "LicenseHeader", ['LicenseKey' => $licenseKey]);
        $this->client->__setSoapHeaders([$header]);
    }

    public function getCategories()
    {
        if ($this->categories === null) {
            $this->loadCategories();
        }
        return $this->categories;
    }

    public function getCategoriesTree()
    {
        if ($this->categoriesTree === null) {
            $this->loadCategories();
        }
        return $this->categoriesTree;
    }

    public function getProductsByCategory($categoryCode)
    {
        $result = [];
        $parametersMap = [
            3 => 'SegmentId',
            6 => 'GroupId',
            9 => 'ClassId',
            12 => 'SeriesId',
        ];
        $idLength = strlen($categoryCode);
        if (isset($parametersMap[$idLength])) {
            $groupParameter = $parametersMap[$idLength];
            $parameters = [
                'GetProductListRequest' => [
                    'Filters' => [
                        [
                            'Name' => $groupParameter,
                            'Value' => $categoryCode,
                        ],
                        [
                            'Name' => 'Language',
                            'Value' => $this->language,
                        ],
                        [
                            'Name' => 'Currency',
                            'Value' => $this->currency,
                        ],
                    ],
                ],
            ];
            try {
                $requestResult = $this->client->GetProductList($parameters);
                $rawData = [];
                if (isset($requestResult->GetProductListResult) && isset($requestResult->GetProductListResult->ProductList)
                    && isset($requestResult->GetProductListResult->ProductList->Product)
                ) {
                    $rawData = $requestResult->GetProductListResult->ProductList->Product;
                }
                foreach ($rawData as $productInfo) {
                    $quantity = (int)$productInfo->Quantity;
                    $product = new AcmeWarehouseProduct($this, $productInfo->SupplierCode);
                    $title = trim(html_entity_decode($productInfo->Name, ENT_QUOTES, 'UTF-8'));
                    $textInfo1 = explode(',', $title);
                    $textInfo2 = explode('/', $title);
                    if (count($textInfo1) > count($textInfo2)) {
                        $textInfo = $textInfo1;
                    } else {
                        $textInfo = $textInfo2;
                    }
                    $product->title = array_shift($textInfo);
                    if (isset($productInfo->Warranty)) {
                        $product->warranty = max(0, (int)$productInfo->Warranty);
                    }
                    $product->description = $title . '<br />';
                    $product->description = str_replace('&', '&amp;', $product->description);
                    $product->price = (float)$productInfo->Price;
                    $product->quantity = $quantity;
                    $product->rrp = (float)$productInfo->RecommendedRetailPrice;
                    $product->vendorCode = $productInfo->VendorId;
                    $product->manufacturerCode = $productInfo->PartNumber;
                    $product->barcode = $this->getProductBarcode($productInfo->SupplierCode);
                    if ($quantity === 0 && isset($productInfo->DateExpected) && $productInfo->DateExpected !== '1900-01-01T00:00:00') {
                        $date = $productInfo->DateExpected;
                        $i = strpos($date, 'T');
                        if ($i !== false) {
                            $date = substr($date, 0, $i);
                        }
                        $parts = explode('-', $date);
                        if (count($parts) === 3) {
                            $date = implode('.', array_reverse($parts));
                            $product->dateExpected = $date;
                        }
                    }
                    $result[] = $product;
                }
            } catch (Exception $e) {
                $this->logError('Soap query problem. ' . $e->getMessage());
            }
        } else {
            $this->logError('Invalid group ID');
        }
        return $result;
    }

    public function getProductBarcode($productCode)
    {
        if ($this->barcodes === null) {
            $this->barcodes = [];
            $parameters = [
                'GetProductBarcodeList' => [],
            ];
            try {
                $requestResult = $this->client->GetProductBarcodeList($parameters);
                if ($requestResult && isset($requestResult->GetProductBarcodeListResult) && isset($requestResult->GetProductBarcodeListResult->ProductBarcodeList)
                    && isset($requestResult->GetProductBarcodeListResult->ProductBarcodeList->ProductBarcode)
                ) {
                    $actualData = $requestResult->GetProductBarcodeListResult->ProductBarcodeList->ProductBarcode;
                    foreach ($actualData as $bardcodeInfo) {
                        $this->barcodes[$bardcodeInfo->SupplierCode] = $bardcodeInfo->Barcode;
                    }
                }
            } catch (Exception $e) {
                $this->logError('Soap query problem. ' . $e->getMessage());
            }
        }
        return isset($this->barcodes[$productCode]) ? $this->barcodes[$productCode] : '';
    }

    public function getProductParameters($productCode)
    {
        $result = [];
        $specification = $this->getProductSpecification($productCode);
        foreach ($specification as &$property) {
            $parameter = new WarehouseParameter($this, $property->PropertyCode);
            $parameter->title = $property->PropertyName;
            $value = trim(html_entity_decode($property->PropertyValue, ENT_QUOTES, 'UTF-8'));
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $parameter->value = $value;
            $result[] = $parameter;
        }
        return $result;
    }

    public function getVendorNameByCode($code)
    {
        if ($this->vendors === null) {
            $this->vendors = [];
            try {
                $parameters = [
                    'GetVendorListRequest' => [
                        'Filters' => [
                            [
                                'Name' => 'Language',
                                'Value' => $this->language,
                            ],
                        ],
                    ],
                ];
                $rawData = [];
                $requestResult = $this->client->GetVendorList($parameters);
                if (isset($requestResult->GetVendorListResult)) {
                    if (isset($requestResult->GetVendorListResult->VendorList)) {
                        if (isset($requestResult->GetVendorListResult->VendorList->Vendor)) {
                            $rawData = $requestResult->GetVendorListResult->VendorList->Vendor;
                        }
                    }
                }
                foreach ($rawData as &$vendorInfo) {
                    $this->vendors[$vendorInfo->VendorId] = $vendorInfo->VendorName;
                }
            } catch (Exception $e) {
                $this->logError('Soap query problem. ' . $e->getMessage());
            }
        }
        return isset($this->vendors[$code]) ? $this->vendors[$code] : '';
    }

    public function getProductResources($supplierCode, $resourceType)
    {
        $result = false;

        $parameters = [
            'GetProductResourcesRequest' => [
                'Filters' => [
                    [
                        'Name' => 'SupplierCode',
                        'Value' => $supplierCode,
                    ],
                    [
                        'Name' => 'Language',
                        'Value' => $this->language,
                    ],
                ],
            ],
        ];
        try {
            $requestResult = $this->client->GetProductResources($parameters);
            if (isset($requestResult->GetProductResourcesResult)) {
                $result = [];
                if (isset($requestResult->GetProductResourcesResult->ProductResources)) {
                    foreach ($requestResult->GetProductResourcesResult->ProductResources as &$info) {
                        if ($info->SupplierCode == $supplierCode) {
                            $resources = $info->ProductResource;
                            foreach ($resources as &$resource) {
                                if ($resource->ResourceType != $resourceType) {
                                    continue;
                                }
                                $image = new WarehouseResource($this);
                                $image->setUrl($resource->ResourceURL);
                                $image->resolveNameFromUrl();
                                $result[] = $image;
                            }
                            break;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. ' . $e->getMessage());
        }
        return $result;
    }

    protected function loadCategories()
    {
        $this->categories = [];
        $this->categoriesTree = [];
        $classification = $this->getProductClassification();
        if ($classification) {
            $groups = [
                'Segment' => $classification->SegmentList->Segment,
                'Group' => $classification->GroupList->Group,
                'Class' => $classification->ClassList->Class,
                //'Series' => $classification->SeriesList->Series,
            ];
            $previousGroupType = '';
            $normalizedInfo = [];
            foreach ($groups as $groupType => &$group) {
                $idField = $groupType . 'Id';
                $titleField = $groupType . 'Name';
                $parentField = $previousGroupType ? $previousGroupType . 'Id' : '';

                foreach ($group as &$item) {
                    $code = $item->$idField;
                    $category = new WarehouseCategory($this, $code);
                    $category->title = $item->$titleField;
                    $category->parentCode = $parentField ? $item->$parentField : 0;
                    $this->categories[$code] = $category;
                    $normalizedInfo[$item->$idField] = $category;
                }
                $previousGroupType = $groupType;
            }

            $childrenMap = [];
            foreach ($normalizedInfo as &$categoryInfo) {
                if (!isset($childrenMap[$categoryInfo->parentCode])) {
                    $childrenMap[$categoryInfo->parentCode] = [];
                }
                $childrenMap[$categoryInfo->parentCode][] = &$categoryInfo;
            }
            unset($categoryInfo);
            foreach ($normalizedInfo as &$categoryInfo) {
                if (isset($childrenMap[$categoryInfo->code])) {
                    $categoryInfo->children = $childrenMap[$categoryInfo->code];
                }
            }
            if ($childrenMap) {
                $this->categoriesTree = $childrenMap[0];
            }
        }
    }

    protected function getProductClassification()
    {
        $classification = null;
        try {
            $requestResult = $this->client->GetProductClassification();
            if (!empty($requestResult->GetProductClassificationResult)) {
                $classification = $requestResult->GetProductClassificationResult;
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. ' . $e->getMessage());
        }
        return $classification;
    }

    protected function getProductSpecification($supplierCode)
    {
        $result = [];
        $parameters = [
            'GetProductSpecificationRequest' => [
                'Filters' => [
                    [
                        'Name' => 'SupplierCode',
                        'Value' => $supplierCode,
                    ],
                    [
                        'Name' => 'Language',
                        'Value' => $this->language,
                    ],
                ],
            ],
        ];
        try {
            $requestResult = $this->client->GetProductSpecification($parameters);
            if (isset($requestResult->GetProductSpecificationResult)) {
                if (isset($requestResult->GetProductSpecificationResult->ProductSpecification)) {
                    foreach ($requestResult->GetProductSpecificationResult->ProductSpecification as &$info) {
                        if ($info->SupplierCode == $supplierCode) {
                            $result = $info->ProductProperty;
                            break;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. ' . $e->getMessage());
        }
        return $result;
    }
}

class AcmeWarehouseProduct extends WarehouseProduct
{
    const RESOURCE_TYPE_SMALL_IMAGE = 0;
    const RESOURCE_TYPE_LINK = 4;
    const RESOURCE_TYPE_MEDIUM_IMAGE = 7;
    const RESOURCE_TYPE_LARGE_IMAGE = 8;
    const RESOURCE_TYPE_HQ_IMAGE = 12;
    const RESOURCE_TYPE_RES_LINK = 13;
    const RESOURCE_TYPE_RES_OTHER = 14;
    const RESOURCE_TYPE_RES_YOUTUBE = 15;

    public function __construct(AcmeWarehouse $warehouse, $code)
    {
        $this->code = $code;
        $this->warehouse = $warehouse;
    }

    public function getImages()
    {
        return $this->warehouse->getProductResources($this->code, self::RESOURCE_TYPE_LARGE_IMAGE);
    }

    public function getVendorName()
    {
        return $this->warehouse->getVendorNameByCode($this->vendorCode);
    }
}