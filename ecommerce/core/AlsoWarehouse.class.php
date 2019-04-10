<?php

class AlsoWarehouse extends Warehouse
    implements MinimumActiveProductsInfoProvider
{
    // qty warehouse id 1 - local, 2 - global
    const CODE = 'also';
    const SERVICE_DOMAIN = 'b2b.also.ee';
    const SUPPLIER_ID = 0;
    protected $clientId;
    protected $username;
    protected $password;
    protected $protocol = 'http';

    public function __construct($clientId, $username, $password)
    {
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
    }

    public function getCategories()
    {
        $result = [];
        $xmlString = file_get_contents($this->protocol . '://' . self::SERVICE_DOMAIN . '/DirectXML.svc/GetGrouping/'
            . self::SUPPLIER_ID . '/' . $this->clientId);
        if ($xmlString) {
            $xml = simplexml_load_string($xmlString);
            if (!$xml) {
                $this->logError('getCategories XML parsing failure');
            } else {
                foreach ($xml->GroupBy as $xmlNode) {
                    $group = (string)$xmlNode['GroupID'];
                    if ($group !== 'ClassID') {
                        continue;
                    }
                    $code = (string)$xmlNode['Value'];
                    $category = new WarehouseCategory($this, $code);
                    $category->title = (string)$xmlNode['Description'];
                    $result[$code] = $category;
                }
            }
        } else {
            $this->logError('getCategories failure, no data received');
        }
        return $result;
    }

    public function getMinimumActiveProductsInfo(array $relevantIds)
    {
        $result = null;
        $supplierId = self::SUPPLIER_ID;
        $parametersXml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<CatalogRequest>
    <Date/>
    <Route>
        <From><ClientID>{$this->clientId}</ClientID></From>
        <To><ClientID>{$supplierId}</ClientID></To>
    </Route>
    <Filters>
        <Filter FilterID="StockLevel" Value="OnStock"/>
        <Filter FilterID="Price" Value="WOVAT"/>
    </Filters>
</CatalogRequest>
EOD;
        $xml = $this->queryXmlInterface($parametersXml);
        if ($xml) {
            $result = [];
            $relevancyIndex = array_flip($relevantIds);
            foreach ($xml->ListofCatalogDetails->CatalogItem as $itemXml) {
                $quantity = 0;
                foreach ($itemXml->Qty as $qtyXml) {
                    if ($qtyXml['WarehouseID'] == '1') {
                        $quantity = (int)$qtyXml->QtyAvailable;
                        break;
                    }
                }
                if ($quantity == 0) {
                    continue;
                }
                $code = (string)$itemXml->Product->ProductID;
                if (!isset($relevancyIndex[$code])) {
                    continue;
                }
                $product = new AlsoWarehouseProduct($this, $code);
                $product->title = (string)$itemXml->Product->Description;
                $product->price = (float)$itemXml->Price->UnitPrice;
                $product->quantity = $quantity;
                $result[] = $product;
            }
        }
        return $result;
    }

    public function getProductsByCategory($categoryCode)
    {
        $result = null;
        $supplierId = self::SUPPLIER_ID;
        $parametersXml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<CatalogRequest>
    <Date/>
    <Route>
        <From><ClientID>{$this->clientId}</ClientID></From>
        <To><ClientID>{$supplierId}</ClientID></To>
    </Route>
    <Filters>
        <Filter FilterID="StockLevel" Value="OnStock"/>
        <Filter FilterID="ClassID" Value="{$categoryCode}"/>
        <Filter FilterID="Price" Value="WOVAT"/>
    </Filters>
</CatalogRequest>
EOD;
        $xml = $this->queryXmlInterface($parametersXml);
        if ($xml) {
            $result = [];
            foreach ($xml->ListofCatalogDetails->CatalogItem as $itemXml) {
                $quantity = 0;
                foreach ($itemXml->Qty as $qtyXml) {
                    // WarehouseID: 1 - local, 2 - global
                    if ($qtyXml['WarehouseID'] == '1') {
                        $quantity = (int)$qtyXml->QtyAvailable;
                        break;
                    }
                }
                if ($quantity == 0) {
                    continue;
                }
                $code = (string)$itemXml->Product->ProductID;
                $product = new AlsoWarehouseProduct($this, $code);
                $product->title = (string)$itemXml->Product->Description;
                $product->description = (string)$itemXml->Product->LongDesc;
                $product->manufacturerCode = (string)$itemXml->Product->PartNumber;
                $product->price = (float)$itemXml->Price->UnitPrice;
                $product->quantity = $quantity;
                $product->barcode = (string)$itemXml->Product->EANCode;
                if (!empty($itemXml->Product->Grouping)) {
                    foreach ($itemXml->Product->Grouping->GroupBy as $groupXml) {
                        if ($groupXml['GroupID'] == 'VendorID') {
                            $product->vendorCode = (string)$groupXml['Value'];
                            break;
                        }
                    }
                }
                if ($spec = $this->getProductSpec($product->manufacturerCode)) {
                    $product->analyseSpec($spec);
                }
                $result[] = $product;
            }
        }
        return $result;
    }

    public function getProductParameters($productCode)
    {
        // TODO: refactor, remove this method declaration from Warehouse?
        return [];
    }

    public function getProductSpec($productPartNumber)
    {
        $parametersXml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<ProductSpecRequest>
	<Date/>
	<Route>
		<From>
			<ClientID>{$this->clientId}</ClientID>
		</From>
		<To>
			<ClientID>0</ClientID>
		</To>
	</Route>
	<Language>ENG</Language>
	<PartNumber>{$productPartNumber}</PartNumber>
</ProductSpecRequest>
EOD;
        return $this->queryXmlInterface($parametersXml);
    }

    protected function queryXmlInterface($parametersXml)
    {
        $result = null;
        $request = new externalRequest(self::SERVICE_DOMAIN, '/DirectXML.svc/0/scripts/XML_Interface.dll', [], 'POST');
        $parametersXml = str_replace("\n", '', $parametersXml);
        $parametersXml = str_replace("\r", '', $parametersXml);
        $request->setRequestBody('USERNAME=' . urlencode($this->username) . '&PASSWORD=' . urlencode($this->password) . '&XML=' . urlencode($parametersXml));
        $request->setContentType('text/html');
        $request->setContentType('application/x-www-form-urlencoded');
        $response = $request->getData();
        if ($response) {
            $result = simplexml_load_string($response);
            if (!$result) {
                $this->logError('Failed parsing response XML.');
            }
        } else {
            $this->logError('XML request failure.');
        }
        return $result;
    }

    public function setProtocol($newProtocol)
    {
        $this->protocol = $newProtocol;
    }
}

class AlsoWarehouseProduct extends WarehouseProduct
{
    protected $spec;
    protected $parameters;
    protected $datasheet;
    protected $mainParameters;

    public function analyseSpec($spec)
    {
        $this->spec = $spec;
        if (isset($spec->ProductDetails->PeriodofWarranty)) {
            $this->warranty = (int)$spec->ProductDetails->PeriodofWarranty;
        }
        $datasheet = $this->getDatasheet();
        if ($datasheet) {
            $doc = new DOMDocument();
            $doc->loadHTML($datasheet);
            $xpath = new DOMXpath($doc);
            $nodes = $xpath->query("//tr[@class='marketingText']/td");
            if ($nodes->length == 1 && trim($nodes->item(0)->nodeValue)) {
                $this->description = $this::getNodeInnerHtml($nodes->item(0));
            }
            $nodes = $xpath->query("//tr[@class='featuresText']/td");
            if ($nodes->length == 1 && trim($nodes->item(0)->nodeValue)) {
                $feats = $this::getNodeInnerHtml($nodes->item(0));
                if ($feats) {
                    if ($this->description) {
                        $this->description .= '<br/>';
                    }
                    $this->description .= $feats;
                }
            }
        }
    }

    public function getImages()
    {
        $result = [];
        $datasheet = $this->getDatasheet();
        if ($datasheet) {
            $doc = new DOMDocument();
            $doc->loadHTML($datasheet);
            $xpath = new DOMXpath($doc);
            $imageNodes = $xpath->query("//table[@class='head']//*[@class='image']/img");
            foreach ($imageNodes as $node) {
                $srcAttribute = $node->attributes->getNamedItem('src');
                if ($srcAttribute && $srcAttribute->value) {
                    $resource = new WarehouseResource($this->warehouse);
                    $resource->setUrl($srcAttribute->value);
                    $resource->resolveNameFromUrl();
                    $result[] = $resource;
                }
            }
        }
        return $result;
    }

    public function getVendorName()
    {
        $result = '';
        $spec = $this->spec;
        if ($spec && !empty($spec->ProductDetails->Manufacturer)) {
            $result = (string)$spec->ProductDetails->Manufacturer;
        }
        return $result;
    }

    public function getParameters()
    {
        $result = $this->getParametersFromSpec([
            'PeriodofWarranty',
            'Weight',
            'Width',
            'Height',
            'Length',
            'Depth',
        ]);
        $datasheet = $this->getDatasheet();
        if ($datasheet) {
            $doc = new DOMDocument();
            $doc->loadHTML($datasheet);
            $xpath = new DOMXpath($doc);
            $parametersRowNodes = $xpath->query("//*[@class='properties']/tr");
            foreach ($parametersRowNodes as $node) {
                $classAttribute = $node->attributes->getNamedItem('class');
                if (!$classAttribute || strpos($classAttribute->value, 'mspec') === false) {
                    continue;
                }
                $cellNodes = $xpath->query('td', $node);
                if ($cellNodes->length == 2) {
                    $parameterName = trim($cellNodes->item(0)->textContent);
                    $parameterValue = trim($cellNodes->item(1)->textContent);

                    $parameter = new WarehouseParameter($this->warehouse, md5($parameterName));
                    $parameter->title = $parameterName;
                    $parameter->value = $parameterValue;
                    $result[] = $parameter;
                }
            }
        }
        return $result;
    }

    protected function getDatasheet()
    {
        if ($this->datasheet === null) {
            $this->datasheet = '';
            $spec = $this->spec;
            if ($spec && !empty($spec->ProductLinks->Link) && (string)$spec->ProductLinks->Link->Name == 'CNET data'
                && (string)$spec->ProductLinks->Link->Value
            ) {
                $dataSheetUrl = trim((string)$spec->ProductLinks->Link->Value);
                $this->datasheet = file_get_contents($dataSheetUrl);
            }
        }
        return $this->datasheet;
    }

    protected function getParametersFromSpec($properties)
    {
        $result = [];
        if ($this->spec) {
            foreach ($properties as $property) {
                if (isset($this->spec->ProductDetails->$property)) {
                    $value = trim((string)$this->spec->ProductDetails->$property);
                    if ($value) {
                        $parameter = new WarehouseParameter($this->warehouse, $property);
                        $parameter->title = $property;
                        $parameter->value = $value;
                        $result[] = $parameter;
                    }
                }
            }
        }
        return $result;
    }

    protected static function getNodeInnerHtml(DOMNode $node)
    {
        $result = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $result .= $child->ownerDocument->saveXML($child);
        }
        return $result;
    }
}