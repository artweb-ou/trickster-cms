<?php

class TdBalticWarehouse extends Warehouse
    implements MinimumActiveProductsInfoProvider, WarehouseCategoriesTreeProvider
{
    const CODE = 'tdbaltic';
    const WAREHOUSE_URL = 'http://tdonline.tdbaltic.net/pls/PROD/';
    protected $credentials = '';
    protected $categoryProductsMap;
    protected $productDataSheetMap;
    protected $productParametersMap = [];
    protected $categories;
    protected $categoriesTree;

    public function __construct($orgNumber, $username, $password)
    {
        $this->credentials = [
            'orgnum' => $orgNumber,
            'username' => $username,
            'pwd' => $password,
        ];
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

    public function getMinimumActiveProductsInfo(array $relevantIds)
    {
        $result = [];
        $xml = $this->downloadXml('ProdCat');
        if ($xml) {
            $relevancyIndex = array_flip($relevantIds);
            foreach ($xml->Product as $productXml) {
                $quantity = (int)$productXml['Stock'];
                $code = (string)$productXml['TDPartNbr'];
                if (!isset($relevancyIndex[$code]) || $quantity == 0) {
                    continue;
                }
                $product = new WarehouseProduct($this, $code);
                $product->price = (float)$productXml['Price'];
                $product->quantity = $quantity;
                $result[] = $product;
            }
        }
        return $result;
    }

    public function getProductsByCategory($categoryCode)
    {
        if ($this->categoryProductsMap === null) {
            $this->categoryProductsMap = [];
            $xml = $this->downloadXml('ProdCat', ['ean' => 'Y']);
            if ($xml) {
                foreach ($xml->Product as $productXml) {
                    $quantity = (int)$productXml['Stock'];
                    $code = (string)$productXml['TDPartNbr'];
                    $product = new WarehouseProduct($this, $code);
                    $product->manufacturerCode = $code;

                    $product->title = (string)$productXml['ProdDesc'];
                    $product->price = (float)$productXml['Price'];
                    $product->quantity = $quantity;
                    $product->vendorCode = (string)$productXml['Manuf'];
                    $product->barcode = (string)$productXml['Ean'];
                    if ($quantity === 0 && isset($productXml['DelDate'])) {
                        $expectedDate = (string)$productXml['DelDate'];
                        if (strlen($expectedDate) === 8) {
                            $prettyDate = substr($expectedDate, -2);
                            $prettyDate .= '.' . substr($expectedDate, 4, 2);
                            $prettyDate .= '.' . substr($expectedDate, 0, 4);
                            $product->dateExpected = $prettyDate;
                        }
                    }
                    $images = [];
                    if ($sheetXml = $this->getProductDataSheet($code)) {
                        foreach ($sheetXml->children() as $parameterXml) {
                            $tagName = (string)$parameterXml->getName();
                            $value = (string)$parameterXml;
                            if (!$value || $value == 'n/a' || $tagName == 'ShortDesc' || $tagName == 'ManufLogo') {
                                continue;
                            }
                            if ($tagName == 'LongDesc') {
                                $product->description = $value;
                            } elseif ($tagName == 'Warranty') {
                                $product->warranty = (int)((float)$value * 12);
                            } elseif (strpos($tagName, 'ProductPicture') === 0) {
                                if ($value != '') {
                                    $image = new WarehouseResource($this);
                                    $image->setUrl($value);
                                    $image->resolveNameFromUrl();
                                    $images[] = $image;
                                }
                            } else {
                                $parameter = new WarehouseParameter($this, $tagName);
                                if ($parameterXml['descr']) {
                                    $parameter->title = (string)$parameterXml['descr'];
                                } else {
                                    $parameter->title = $tagName;
                                }
                                $parameter->value = $value;
                                $this->cacheProductParameter($code, $parameter);
                            }
                        }
                    }
                    $product->setImages($images);

                    $productCategoryCode = $this->parseCategoryCode((string)$productXml['ClassCode']);
                    $productSubCategoryCode = 'sub!!' . $this->parseCategoryCode((string)$productXml['SubClassCode']);
                    $this->cacheProductByCategory($product, $productCategoryCode);
                    $this->cacheProductByCategory($product, $productSubCategoryCode);
                }
            }
        }
        return isset($this->categoryProductsMap[$categoryCode]) ? $this->categoryProductsMap[$categoryCode] : [];
    }

    public function getProductParameters($productCode)
    {
        return isset($this->productParametersMap[$productCode]) ? $this->productParametersMap[$productCode] : [];
    }

    protected function loadCategories()
    {
        $this->categories = [];
        $this->categoriesTree = [];
        $xml = $this->downloadXml('FamilyClass');

        if ($xml) {
            foreach ($xml->Class as $class) {
                $categoryCode = $this->parseCategoryCode((string)$class['Code']);
                $category = new WarehouseCategory($this, $categoryCode);
                $category->title = (string)$class['Name'];
                $this->categories[$categoryCode] = $category;

                foreach ($class->SubClass as $subClass) {
                    $categoryCode = 'sub!!' . $this->parseCategoryCode((string)$subClass['Code']);
                    $childCategory = new WarehouseCategory($this, $categoryCode);
                    $childCategory->title = (string)$subClass['Name'];
                    $category->children[] = $childCategory;
                    $this->categories[$categoryCode] = $childCategory;
                }
                $this->categoriesTree[] = $category;
            }
        }
    }

    protected function parseCategoryCode($input)
    {
        return str_replace(['"', "'", '>', '<', '&'], '', $input);
    }

    /**
     * @param $productCode
     * @return SimpleXMLElement
     */
    protected function getProductDataSheet($productCode)
    {
        if ($this->productDataSheetMap === null) {
            $xml = $this->downloadXml('DSheets');
            foreach ($xml->Datasheet as $sheet) {
                $this->productDataSheetMap[(string)$sheet['TDPartNbr']] = $sheet;
            }
        }
        return isset($this->productDataSheetMap[$productCode]) ? $this->productDataSheetMap[$productCode] : null;
    }

    /**
     * @param $xmlName
     * @param array $arguments
     * @return SimpleXMLElement
     */
    protected function downloadXml($xmlName, array $arguments = [])
    {
        $result = null;

        $arguments = $this->credentials + $arguments;
        $url = self::WAREHOUSE_URL . 'ixml.' . $xmlName . '?' . http_build_query($arguments);
        $data = file_get_contents($url);
        if ($data !== false) {
            $xml = simplexml_load_string($data);
            if ($xml !== false) {
                if (isset($xml->Exception)) {
                    $this->logError('Error message in XML: ' . $xml->Exception['Message']);
                } else {
                    $result = $xml;
                }
            } else {
                $this->logError($xmlName . 'XML parsing failed');
            }
        } else {
            $this->logError($xmlName . 'XML download failed');
        }
        return $result;
    }

    protected function cacheProductByCategory(WarehouseProduct $product, $categoryCode)
    {
        if (!isset($this->categoryProductsMap[$categoryCode])) {
            $this->categoryProductsMap[$categoryCode] = [];
        }
        $this->categoryProductsMap[$categoryCode][] = $product;
    }

    protected function cacheProductParameter($productCode, WarehouseParameter $parameter)
    {
        if (!isset($this->productParametersMap[$productCode])) {
            $this->productParametersMap[$productCode] = [];
        }
        $this->productParametersMap[$productCode][] = $parameter;
    }
}