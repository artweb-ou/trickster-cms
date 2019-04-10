<?php

class AbcWarehouse extends Warehouse
    implements MinimumActiveProductsInfoProvider
{
    const CODE = 'abc';
    const WAREHOUSE_URL = 'https://gateway.systemb2b.com/';
    protected $username = '';
    protected $password = '';
    protected $clients = [];
    protected $dictionaries;
    protected $parameters;
    protected $vendors;
    protected $categoryProductsMap;
    protected $productCodeEanMap;
    protected $debug = false;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getCategories()
    {
        $result = [];
        //        $requestResult = $client->GetPriceListHierarchy();
        //        if (isset($requestResult->GetPriceListHierarchyResult)
        //            && isset($requestResult->GetPriceListHierarchyResult->any)
        //        ) {
        //            $xml = simplexml_load_string($requestResult->GetPriceListHierarchyResult->any);
        //        }
        if ($categoryDictionary = $this->getDictionary('MinorGroup')) {
            foreach ($categoryDictionary as $categoryXml) {
                $code = (string)$categoryXml->EntryId;
                $category = new WarehouseCategory($this, $code);
                $category->title = (string)$categoryXml->EntryName;
                $result[$code] = $category;
            }
        }
        return $result;
    }

    public function getMinimumActiveProductsInfo(array $relevantIds)
    {
        $result = null;
        $productsXml = $this->downloadProductsList('PRODUCTSCOMPACT', 'STOCKAVAILABLE');

        if ($productsXml) {
            $result = [];
            $relevancyIndex = array_flip($relevantIds);

            foreach ($productsXml->Products->Product as $productXml) {
                $code = (string)$productXml->ProductId;
                $quantity = (int)$productXml->Available;
                if (!isset($relevancyIndex[$code]) || $quantity == 0) {
                    continue;
                }
                $product = new WarehouseProduct($this, $code);
                $product->quantity = $quantity;
                $product->price = (float)$productXml->Price;
                $result[] = $product;
            }
        }
        return $result;
    }

    public function getProductsByCategory($categoryCode)
    {
        $result = null;
        if ($this->categoryProductsMap === null) {
            $this->categoryProductsMap = false;
            $productsXml = $this->downloadProducts();

            if ($productsXml) {
                $this->categoryProductsMap = [];
                foreach ($productsXml->Products->Product as $productXml) {
                    $quantity = (int)$productXml->Available;
                    if ($quantity == 0) {
                        continue;
                    }
                    $code = (string)$productXml->ProductId;
                    $product = new AbcWarehouseProduct($this, $code);
                    $product->title = (string)$productXml->ProductName;
                    $product->price = (float)$productXml->Price;
                    $product->quantity = $quantity;
                    $product->vendorCode = (string)$productXml->ProducerId;
                    $product->manufacturerCode = (string)$productXml->PartNumber;
                    if (isset($productXml->BarCodes) && isset($productXml->BarCodes->BarCodeInfo)) {
                        foreach ($productXml->BarCodes->BarCodeInfo as $barcodeXml) {
                            if ((string)$barcodeXml->BarCodeType == 'EAN13') {
                                $product->barcode = (string)$barcodeXml->BarCode;
                                break;
                            }
                        }
                    }
                    $productGroup = (string)$productXml->MinorGroup;
                    if (!isset($this->categoryProductsMap[$productGroup])) {
                        $this->categoryProductsMap[$productGroup] = [];
                    }
                    $product->barcode = $this->getProductBarcode($product->code);
                    $this->categoryProductsMap[$productGroup][] = $product;
                }
            }
        }
        if ($this->categoryProductsMap !== false) {
            $result = isset($this->categoryProductsMap[$categoryCode]) ? $this->categoryProductsMap[$categoryCode] : [];
        }
        return $result;
    }

    public function getProductParameters($productCode)
    {
        $result = [];
        $description = $this->downloadProductDescription($productCode);
        if ($description) {
            foreach ($description as $parameterXml) {
                $parameterId = (string)$parameterXml->TypeId;
                $value = (string)$parameterXml->Value . (string)$parameterXml->UnitMeasureName;
                $parameter = $this->getParameterById($parameterId);
                if ($parameter && $value) {
                    $parameter = clone($parameter);
                    $parameter->value = $value;
                    $result[] = $parameter;
                }
            }
        }
        return $result;
    }

    public function getVendorNameByCode($code)
    {
        if ($this->vendors === null) {
            $this->vendors = [];
            $dictionary = $this->getDictionary('ProducerId');
            if ($dictionary) {
                foreach ($dictionary as $dictionaryEntryXml) {
                    $entryId = trim((string)$dictionaryEntryXml->EntryId);
                    $this->vendors[$entryId] = trim((string)$dictionaryEntryXml->EntryName);
                }
            }
        }
        return isset($this->vendors[$code]) ? $this->vendors[$code] : '';
    }

    public function getProductImages($productCode)
    {
        $result = [];
        $client = $this->getClient('Resources');
        try {
            $data = $client->GetResources([
                'ResourceClass' => 'PRODUCT',
                'ResourceKey' => $productCode,
            ]);
            if ($data && isset($data->GetResourcesResult) && isset($data->GetResourcesResult->any)) {
                $xml = simplexml_load_string($data->GetResourcesResult->any);
                foreach ($xml->Data->Resource as $resourceXml) {
                    if ((string)$resourceXml->Type != 'IMG'
                        || (string)$resourceXml->SubType != 'MAIN'
                    ) {
                        continue;
                    }
                    $fileName = (string)$resourceXml->Name;
                    $image = new AbcWarehouseResource($this, $fileName);

                    if ($resourceXml['AsBytes'] == 1) {
                        $url = self::WAREHOUSE_URL . 'pageset/resource.aspx?ResourceClass=PRODUCT&ResourceKey='
                            . $productCode . '&ResourceType=IMG&ResourceSubType=MAIN&ResourceName=' . $fileName;
                        $image->setUrl($url);
                    } elseif ($resourceXml['AsDoc'] == 1) {
                        $productImageData = $this->downloadProductImage($productCode, $fileName);
                        $image->setContent($productImageData);
                    }
                    $result[] = $image;
                }
            }
        } catch (Exception $e) {
            $suppressed = false;
            if (isset($e->detail->gatewayerrors->error)) {
                $code = $e->detail->gatewayerrors->error->code;
                $suppressed = $code === '33000'; // no resource for this prod
                if (!$suppressed) {
                    $description = $e->detail->gatewayerrors->error->info;
                    $msg = 'Gateway error ' . $code
                        . ' - ' . $description;
                    $this->logError($msg);
                }
            }
            if (!$suppressed) {
                $this->logError('GetResources Fail. Exception: ' . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * @param $serviceName
     * @return SoapClient
     */
    protected function getClient($serviceName)
    {
        if (!isset($this->clients[$serviceName])) {
            $client = null;
            try {
                $client = new SoapClient(self::WAREHOUSE_URL . 'WS/' . $serviceName . '.asmx?WSDL', [
                    'login' => $this->username,
                    'password' => $this->password,
                    'trace' => true,
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                    // 'connection_timeout' => 15,
                    'keep_alive' => false,
                ]);
                ini_set("default_socket_timeout", 120); // how long to wait for response
            } catch (Exception $e) {
                $this->logError('getClient fail (' . $serviceName . '). Exception: ' . $e->getMessage());
            }
            $this->clients[$serviceName] = $client;
        }
        return $this->clients[$serviceName];
    }

    protected function getParameterById($id)
    {
        $this->getParameters();
        return isset($this->parameters[$id]) ? $this->parameters[$id] : null;
    }

    protected function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = [];
            $dictionary = $this->getDictionary('AttribTypes');
            if ($dictionary) {
                foreach ($dictionary as $dictionaryEntryXml) {
                    $parameterId = (string)$dictionaryEntryXml->EntryId;
                    $parameter = new WarehouseParameter($this, $parameterId);
                    $parameter->title = (string)$dictionaryEntryXml->EntryName;
                    $this->parameters[$parameterId] = $parameter;
                }
            }
        }
        return $this->parameters;
    }

    protected function downloadProducts()
    {
        $result = null;
        if ($this->debug && file_exists('abc_products.xml')) {
            $result = simplexml_load_file('abc_products.xml');
        }
        $client = $this->getClient('PriceList');
        if ($client) {
            try {
                $data = $client->GetProducts();
                if ($data && isset($data->GetProductsResult)
                    && isset($data->GetProductsResult->any)
                ) {
                    if ($this->debug) {
                        file_put_contents('abc_products.xml', (string)$data->GetProductsResult->any);
                    }
                    $result = simplexml_load_string($data->GetProductsResult->any);
                }
            } catch (Exception $e) {
                $this->logError('GetProducts fail. Soap exception: ' . $e->getMessage());
            }
        }
        return $result;
    }

    protected function downloadProductsList($listType, $listParameter)
    {
        $result = null;
        $client = $this->getClient('PriceList');
        if ($client) {
            try {
                $data = $client->GetProductsList([
                    'ListType' => $listType,
                    'ListParameter' => $listParameter,
                ]);
                if ($data && isset($data->GetProductsListResult)
                    && isset($data->GetProductsListResult->any)
                ) {
                    $result = simplexml_load_string($data->GetProductsListResult->any);
                }
            } catch (Exception $e) {
                $this->logError('GetProductsList fail (' . $listType . ', ' . $listParameter
                    . '). Soap exception: ' . $e->getMessage());
            }
        }
        return $result;
    }

    protected function downloadProductDescription($productId, $mode = 'XML')
    {
        $result = [];
        $client = $this->getClient('PriceList');
        if ($client) {
            try {
                $data = $client->GetProductDescription([
                    'ProductId' => $productId,
                    'Mode' => $mode,
                ]);
                if ($data && isset($data->GetProductDescriptionResult)
                    && isset($data->GetProductDescriptionResult->any)
                ) {
                    $xml = simplexml_load_string($data->GetProductDescriptionResult->any);

                    if (!isset($xml->Products) || !isset($xml->Products->Product) || !isset($xml->Products->Product->Descriptions) || !isset($xml->Products->Product->Descriptions->Description)) {
                        return $result;
                    }
                    $descriptionXml = $xml->Products->Product->Descriptions->Description;
                    $result = $descriptionXml->Data->Values->ValueData;
                }
            } catch (Exception $e) {
                // ignore - exception thrown if the product has no description
            }
        }
        return $result;
    }

    protected function getDictionary($id)
    {
        if ($this->dictionaries === null) {
            $this->dictionaries = [];
            $dictionariesXml = $this->downloadDictionaries();
            if ($dictionariesXml) {
                foreach ($dictionariesXml->Dictionary as $dictionaryXml) {
                    $identifier = (string)$dictionaryXml['DictionaryId'];
                    if ($identifier) {
                        $this->dictionaries[$identifier] = $dictionaryXml->children();
                    }
                }
            }
        }
        return isset($this->dictionaries[$id]) ? $this->dictionaries[$id] : null;
    }

    protected function downloadDictionaries()
    {
        $result = null;
        $client = $this->getClient('PriceList');
        if ($client) {
            try {
                $requestResult = $client->GetPriceListDictionaries();
                if (isset($requestResult->GetPriceListDictionariesResult)
                    && isset($requestResult->GetPriceListDictionariesResult->any)
                ) {
                    $result = simplexml_load_string($requestResult->GetPriceListDictionariesResult->any);
                }
            } catch (Exception $e) {
                $this->logError('GetPriceListDictionaries fail. Soap exception: ' . $e->getMessage());
            }
        }
        return $result;
    }

    protected function downloadProductImage($productCode, $imageName)
    {
        $result = '';
        $client = $this->getClient('Resources');
        try {
            $data = $client->GetResource([
                'ResourceClass' => 'PRODUCT',
                'ResourceKey' => $productCode,
                'ResourceType' => 'IMG',
                'ResourceSubType' => 'MAIN',
                'ResourceName' => $imageName,
            ]);
            if ($data && isset($data->GetResourceResult) && isset($data->GetResourceResult->any)) {
                $xml = simplexml_load_string($data->GetResourceResult->any);
                if ($xml && $xml->Body) {
                    $result = base64_decode((string)$xml->Body);
                }
            }
        } catch (Exception $e) {
            $this->logError('GetResource fail (' . $productCode . ', ' . $imageName
                . '). Soap exception: ' . $e->getMessage());
        }
        return $result;
    }

    protected function getProductBarcode($productCode)
    {
        if ($this->productCodeEanMap === null) {
            $this->productCodeEanMap = [];
            $xml = $this->downloadProductsCodes();
            if ($xml && !empty($xml->ProductsCodes->Product)) {
                foreach ($xml->ProductsCodes->Product as $productXml) {
                    if (strtoupper((string)$productXml->BarCodeType) === 'EAN13') {
                        $this->productCodeEanMap[(string)$productXml->ProductId] = (string)$productXml->BarCode;
                    }
                }
            }
        }
        return isset($this->productCodeEanMap[$productCode]) ? $this->productCodeEanMap[$productCode] : '';
    }

    protected function downloadProductsCodes()
    {
        $result = null;
        try {
            $client = $this->getClient('PriceList');
            $data = $client->GetProductsCodes();
            if ($data && isset($data->GetProductsCodesResult) && isset($data->GetProductsCodesResult->any)) {
                $result = simplexml_load_string($data->GetProductsCodesResult->any);
            }
        } catch (Exception $e) {
            $this->logError('downloadProductCodes fail. Soap exception: ' . $e->getMessage());
        }
        return $result;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}

class AbcWarehouseProduct extends WarehouseProduct
{
    public function getImages()
    {
        return $this->warehouse->getProductImages($this->code);
    }

    public function getVendorName()
    {
        return $this->warehouse->getVendorNameByCode($this->vendorCode);
    }
}

class AbcWarehouseResource extends WarehouseResource
{
    protected function download()
    {
        $authHeader = 'Authorization: Basic ' . base64_encode($this->warehouse->getUsername() . ':' . $this->warehouse->getPassword());
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $authHeader,
                'timeout' => 10,
            ],
        ]);
        $this->content = file_get_contents($this->url, null, $context);
    }
}