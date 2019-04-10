<?php

class ElkoWarehouse extends Warehouse
    implements WarehouseCategoriesTreeProvider
{
    const CODE = 'elko';
    protected $client;
    protected $username = '';
    protected $password = '';
    protected $categories;
    protected $categoriesTree;

    public function __construct($username, $password)
    {
        $this->client = new SoapClient('https://ecom.elkogroup.com/xml/listener.asmx?WSDL', [
            'trace' => true,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'keep_alive' => false,
        ]);
        $this->username = $username;
        $this->password = $password;
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
        $result = $this->getProductList($categoryCode);
        return $result;
    }

    public function getProductParameters($productCode)
    {
        return $this->getProductDescription($productCode);
    }

    protected function getProductList($categoryCode = '', $vendorCode = '', $elkoCode = '')
    {
        $result = [];
        try {
            $requestResult = $this->client->ProductList($this->composeParameters([
                'CategoryCode' => $categoryCode,
                'VendorCode' => $vendorCode,
                'ELKOcode' => $elkoCode,
            ]));
            if (isset($requestResult->ProductListResult) && isset($requestResult->ProductListResult->any)) {
                $xml = simplexml_load_string($requestResult->ProductListResult->any);
                if ($xml) {
                    if (isset($xml->Error) && isset($xml->Error->Description)) {
                        $this->logError('Failed retrieving products. Error: ' . $xml->Error->Description);
                        $result = null;
                    } else {
                        $unavailableProducts = [];
                        foreach ($xml->Product as $productXml) {
                            $quantity = (int)$productXml->stockQuantity; // TODO: use packagingQuantity or reservedQuantity?
                            //                            if ($quantity == 0) {
                            //                                continue;
                            //                            }
                            $code = (string)$productXml->ELKOcode;
                            $product = new ElkoWarehouseProduct($this, $code);
                            $product->title = (string)$productXml->productName;
                            $product->price = (float)$productXml->price; // TODO: use discountPrice instead?
                            $product->quantity = $quantity;
                            $product->vendorCode = (string)$productXml->vendorName;
                            $product->manufacturerCode = (string)$productXml->manufacturerCode;
                            $product->barcode = (string)$productXml->EANcode;
                            if (isset($productXml->warranty)) {
                                $product->warranty = (int)$productXml->warranty;
                            }
                            if ($productXml->imageLarge && (string)$productXml->imageLarge) {
                                $image = new WarehouseResource($this);
                                $image->setUrl((string)$productXml->imageLarge);
                                $image->resolveNameFromUrl();
                                $product->setImages([$image]);
                            }
                            if ($quantity === 0) {
                                $unavailableProducts[$code] = $product;
                            }
                            $result[] = $product;
                        }
                        if ($unavailableProducts) {
                            $productCodes = array_keys($unavailableProducts);
                            // TODO: assign arrival dates to out of stock products
                            //                             $availabilityInfo = $this->getAvailabilityInfo($productCodes);
                            //                            $test = json_decode(json_encode($availabilityInfo));
                            //                            if (isset($availabilityInfo->Stock->Item)) {
                            //                                foreach ($availabilityInfo->Stock->Item as $itemXml) {
                            //
                            //                                }
                            //                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. Message: ' . $e->getMessage());
        }
        return $result;
    }

    protected function getAvailabilityInfo(array $productsCodes)
    {
        $result = [];
        if (!$productsCodes) {
            return $result;
        }
        return $this->client->CheckAvailability1($this->composeParameters([
            'ELKOcode' => implode(',', $productsCodes),
        ]));
    }

    protected function getProductDescription($elkoCode)
    {
        $result = [];
        try {
            $requestResult = $this->client->ProductDescription($this->composeParameters(['ELKOcode' => $elkoCode]));
            if (isset($requestResult->ProductDescriptionResult)
                && isset($requestResult->ProductDescriptionResult->any)
            ) {
                $xml = simplexml_load_string($requestResult->ProductDescriptionResult->any);
                if ($xml) {
                    $redundantParameters = ['Image' => true, 'Description' => true];

                    foreach ($xml->Product as $parameterXml) {
                        $title = trim((string)$parameterXml->Criteria);
                        $value = trim((string)$parameterXml->Value);

                        if (!$title || !$value) {
                            continue;
                        }
                        if ($title === 'Description') {
                            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                        } else {
                            $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
                        }

                        $parameter = new WarehouseParameter($this, $title);
                        $parameter->title = $title;
                        $parameter->value = $value;
                        $parameter->redundant = isset($redundantParameters[$title]);
                        if (isset($parameterXml->measurement) && ($measurement = (string)$parameterXml->measurement)) {
                            $parameter->value .= $measurement;
                        }
                        $parameter->description = (string)$parameterXml->ComplexName;
                        $result[] = $parameter;
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. Message: ' . $e->getMessage());
        }
        return $result;
    }

    protected function loadCategories()
    {
        $this->categories = [];
        $this->categoriesTree = [];
        try {
            $requestResult = $this->client->CategoryTreeV2($this->composeParameters());
            if (isset($requestResult->CategoryTreeV2Result)
                && isset($requestResult->CategoryTreeV2Result->any)
            ) {
                $xml = simplexml_load_string($requestResult->CategoryTreeV2Result->any);
                if ($xml) {
                    $this->categoriesTree = $this->parseCategoriesFromXml($xml);
                }
            }
        } catch (Exception $e) {
            $this->logError('Soap query problem. Message: ' . $e->getMessage());
        }
    }

    protected function parseCategoriesFromXml(SimpleXMLElement $categoriesXml)
    {
        $result = [];
        if ($categoriesXml->category) {
            foreach ($categoriesXml->category as $categoryXml) {
                $code = (string)$categoryXml->code;
                $category = new WarehouseCategory($this, $code);
                $category->title = (string)$categoryXml->name;
                if (isset($categoryXml->category)) {
                    $category->children = $this->parseCategoriesFromXml($categoryXml);
                } elseif ($code) {
                    $this->categories[$code] = $category;
                }
                $result[] = $category;
            }
        } elseif ($categoriesXml->Error->Description) {
            $this->logError('Categories error: ' . (string)$categoriesXml->Error->Description);
        }
        return $result;
    }

    protected function composeParameters(array $parameters = [])
    {
        $parameters['Username'] = $this->username;
        $parameters['Password'] = $this->password;
        return $parameters;
    }
}

class ElkoWarehouseProduct extends WarehouseProduct
{
    protected $parameters = [];

    public function __construct(ElkoWarehouse $warehouse, $code)
    {
        parent::__construct($warehouse, $code);

        $this->parameters = $this->warehouse->getProductParameters($this->code);
        foreach ($this->parameters as &$parameter) {
            if ($parameter->title === 'Description') {
                $this->description = $parameter->value;
                break;
            }
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}