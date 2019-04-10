<?php

define('ACC_IMPORT_IMAGERESOURCETYPE', 8);

class CsvImportManager extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $errorCode = false;
    protected $errorLocationLineNumber = false;
    protected $errorFieldName = false;
    protected $errorArgument = false;
    protected $file;
    protected $templateColumns;
    protected $productsList;
    protected $categoryId = false;
    protected $languageCode;
    protected $csvData;
    protected $currentGroupId;
    protected $importVendorInfo;
    protected $productsImportManager;
    protected $updatedProductStatuses = [];
    protected $disabledProducts = [];
    protected $priceAdjustment;
    protected $importOrigin;
    protected $language;
    protected $currency;
    protected $fieldsCount;
    protected $delimiter;
    protected $importFirstRow = true;

    public function __construct()
    {
        $this->importOrigin = 'csv';
        $this->productsImportManager = $this->getService('productsImportManager');
    }

    public function checkFile()
    {
        $cachePath = $this->getService('PathsManager')->getPath('uploadsCache');
        $fullName = $cachePath . $this->file;
        if ($this->file && ($handle = fopen($fullName, "r")) !== false) {
            setlocale(LC_ALL, 'en_US.UTF8');
            $rowNumber = 0;
            $result = true;
            $delimiter = $this->delimiter ? $this->delimiter : ";";
            while (($data = fgetcsv($handle, null, $delimiter, '"')) !== false) {
                if ($rowNumber == 0 && !$this->importFirstRow) {
                    $rowNumber++;
                    continue;
                }
                //empty rows don't need to be imported or checked for errors
                if (!$this->emptyRow($data)) {
                    //row is not empty, let's check it's length and mandatory fields
                    if (!$this->checkRow($rowNumber, $data)) {
                        //break import if problem is found
                        $result = false;
                        break;
                    }
                    $rowNumber++;
                    $this->csvData[] = $data;
                }
            }
            fclose($handle);
            if ($result) {
                $result = $this->parseCsvData();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    protected function checkRow($rowNumber, $rowData)
    {
        // check if any mandatory field in this row is missing
        foreach ($this->templateColumns as &$templateColumn) {
            if (!isset($rowData[$templateColumn["columnNumber"] - 1]) || ($templateColumn['mandatory'] == 1) && (trim($rowData[$templateColumn["columnNumber"] - 1]) === "")
            ) {
                $this->setError("missing_mandatory_field", $rowNumber + 1, false, $templateColumn["columnName"]);
                return false;
            }
        }

        return true;
    }

    protected function emptyRow($rowData)
    {
        $emptyRow = true;
        foreach ($rowData as &$field) {
            if (trim($field) != '') {
                $emptyRow = false;
                break;
            }
        }
        return $emptyRow;
    }

    public function parseCsvData()
    {
        $this->productsList = [];
        $result = true;
        foreach ($this->csvData as $rowNumber => &$data) {
            $productInfo = [];
            $productInfo["parameters"] = [];
            $productInfo["images"] = [];
            $productInfo["importOrigin"] = $this->importOrigin;

            foreach ($this->templateColumns as &$templateColumn) {
                if (isset($data[$templateColumn["columnNumber"] - 1])) {
                    $importValue = $data[$templateColumn["columnNumber"] - 1];

                    switch ($templateColumn["productVariable"]) {
                        case "parameter":
                            $possibleValues = explode(';', $importValue);
                            foreach ($possibleValues as &$possibleValue) {
                                if (trim($possibleValue, "\xc2\xa0 \t\n\0") != "") {
                                    $parameterData["importId"] = "csv" . $templateColumn["elementId"];
                                    $parameterData["elementId"] = $templateColumn["elementId"];
                                    $parameterData["value"] = $possibleValue;
                                    $productInfo["parameters"][] = $parameterData;
                                }
                            }
                            break;
                        case "connectedProducts":
                            $possibleValues = explode(';', $importValue);
                            foreach ($possibleValues as &$connectedProductId) {
                                if (($connectedProductId = trim($connectedProductId, "\xc2\xa0 \t\n\0")) != '') {
                                    $productInfo["connectedProducts"][] = $connectedProductId;
                                }
                            }

                            break;
                        case "connectedCategories":
                            $possibleValues = explode(';', $importValue);
                            foreach ($possibleValues as &$connectedCategoryCode) {
                                if (($connectedCategoryCode = trim($connectedCategoryCode, "\xc2\xa0 \t\n\0")) != '') {
                                    $productInfo["connectedCategories"][] = $connectedCategoryCode;
                                }
                            }

                            break;
                        case "images":
                            $productInfo["images"] = explode(",", $importValue);
                            $imagesPath = $this->getService('PathsManager')->getPath('csvImportImages');
                            foreach ($productInfo["images"] as $key => &$image) {
                                if (is_file($imagesPath . $image)) {
                                    $image = $imagesPath . $image;
                                } else {
                                    unset($productInfo["images"][$key]);
                                }
                            }
                            break;
                        case "pdf":
                            $productInfo[$templateColumn["productVariable"]] = ROOT_PATH . "maskuExport/pdf/" . $importValue;
                            break;
                        case "importId":
                            $productInfo[$templateColumn["productVariable"]] = $importValue;
                            break;
                        case "categoryCode":
                        case "categoryCode0":
                        case "categoryCode1":
                        case "categoryCode2":
                        case "categoryCode3":
                        case "categoryCode4":
                        case "categoryCode5":
                            $possibleValues = explode(';', $importValue);
                            foreach ($possibleValues as &$categoryCode) {
                                if (($categoryCode = trim($categoryCode, "\xc2\xa0 \t\n\0")) != '') {
                                    $productInfo[$templateColumn["productVariable"]][] = $categoryCode;
                                }
                            }
                            break;
                        case "quantity":
                            if (!isset($productInfo['quantity'])) {
                                $productInfo['quantity'] = 0;
                            }
                            $productInfo['quantity'] += $importValue;
                            break;
                        case "minimumOrder":
                            $productInfo['minimumOrder'] += $importValue;
                            break;
                        case "price":
                            $importValue = str_replace([" ", ','], ["", '.'], $importValue);
                            $importValue = floatval($importValue);
                            if ($this->priceAdjustment > 0) {
                                $productInfo['price'] = $this->priceAdjustment * $importValue;
                            } else {
                                $productInfo['price'] = $importValue;
                            }
                            break;
                        default:
                            $productInfo[$templateColumn["productVariable"]] = trim($importValue, "\xc2\xa0 \t\n\0");
                    }
                } else {
                    $result = false;
                    $this->setError("missing_field", $rowNumber + 1, $templateColumn["columnName"]);
                    break;
                }
            }
            if (!isset($productInfo["importId"]) || !$productInfo["importId"]) {
                $result = false;
                $this->setError("missing_importid", $rowNumber + 1);
            }
            if (isset($productInfo["quantity"])) {
                $productInfo["availability"] = 'quantity_dependent';
            } else {
                $productInfo["availability"] = "available";
            }

            if (!$result) {
                break;
            }

            $this->productsList[] = $productInfo;
        }
        return $result;
    }

    protected function importCategoriesLevel($levels, $parentId = null, &$categoryIdList)
    {
        if ($parentId === null) {
            $parentId = $this->getService('structureManager')->getElementIdByMarker('categories');
        }
        if ($parentId) {
            if ($levelCodes = array_shift($levels)) {
                $levelCategories = [];
                foreach ($levelCodes as $categoryCode) {
                    $category = $this->productsImportManager->importCategory(
                        $categoryCode,
                        $parentId,
                        $this->importOrigin
                    );

                    if ($category) {
                        $categoryIdList[] = $category->id;
                    }

                    $levelCategories[] = $category;
                }

                foreach ($levelCategories as $levelCategory) {
                    $this->importCategoriesLevel($levels, $levelCategory->id, $categoryIdList);
                }
            }
        }
    }

    public function import()
    {
        $this->productsImportManager->setImportLanguageCode($this->languageCode);

        $productsIndex = $this->productsImportManager->getExistingProductsIndex($this->importOrigin);
        $disabledProducts = [];
        foreach ($productsIndex as $productImportId => &$productInfo) {
            $disabledProducts[$productImportId] = true;
        }

        foreach ($this->productsList as &$productInfo) {
            $productInfo["categoryIdList"] = [];
            $categoriesLevels = [];
            for ($i = 0; $i < 4; $i++) {
                if (isset($productInfo["categoryCode" . $i])) {
                    $categoriesLevels[] = $productInfo["categoryCode" . $i];
                }
            }

            $this->importCategoriesLevel($categoriesLevels, null, $productInfo["categoryIdList"]);
        }

        foreach ($this->productsList as $key => &$productInfo) {
            echo $key . ': ' . $productInfo["importId"] . '<br>';
            flush();

            $this->productsImportManager->importProductInfo($productInfo, $this->importOrigin);

            if ($productInfo["parameters"]) {
                $this->productsImportManager->importInfoForExistingParameters($productInfo["parameters"], $productInfo["importId"], $this->categoryId, $this->importOrigin);
            }

            $this->productsImportManager->importImages($productInfo["images"], $productInfo["importId"], $this->importOrigin);
            if (isset($productInfo["pdf"])) {
                $this->productsImportManager->importFile($productInfo["pdf"], "pdfFile", $productInfo["importId"], $this->importOrigin);
            }
            if (isset($productInfo["brand"])) {
                $brandInfo = [];
                $brandInfo['importOrigin'] = $this->importOrigin;
                $brandInfo['importId'] = $productInfo["brand"];
                $brandInfo['title'] = $productInfo["brand"];
                $this->productsImportManager->importBrandInfo($brandInfo);
                $this->productsImportManager->checkBrandLink($brandInfo, $productInfo["importId"], $this->importOrigin);
            }
            if ($this->categoryId) {
                $this->productsImportManager->checkCategoryLink($productInfo["importId"], $this->categoryId, $this->importOrigin);
            }
            if (isset($productInfo["categoryCode"])) {
                foreach ($productInfo["categoryCode"] as $categoryCode) {
                    $this->productsImportManager->importCategory($categoryCode, $this->categoryId, $this->importOrigin);
                    $this->productsImportManager->checkCategoryLinkByImportId($productInfo["importId"], $categoryCode, $this->importOrigin);
                }
            }
            foreach ($productInfo["categoryIdList"] as &$categoryId) {
                $this->productsImportManager->checkCategoryLink($productInfo["importId"], $categoryId, $this->importOrigin);
            }

            unset($disabledProducts[$productInfo["importId"]]);
        }

        if (isset($productInfo['connectedProducts'])) {
            foreach ($this->productsList as &$productInfo) {
                $this->productsImportManager->checkConnectedProductsLinks($productInfo["importId"], $productInfo['connectedProducts'], $this->importOrigin);
            }
        }

        $this->disableMissingProducts(array_keys($disabledProducts));
    }

    // TODO: possibly move this method to import manager?
    protected function disableMissingProducts($missingProducts)
    {
        $this->productsImportManager->disableProducts($missingProducts);
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function setCategoryId($id)
    {
        $this->categoryId = $id;
    }

    public function setLanguageCode($code)
    {
        $this->languageCode = $code;
    }

    public function setTemplateColumns($templateData)
    {
        $this->templateColumns = $templateData;

        $this->fieldsCount = 0;
        foreach ($templateData as &$column) {
            if ($column['columnNumber'] > $this->fieldsCount) {
                $this->fieldsCount = $column['columnNumber'];
            }
        }
    }

    protected function setError($errorCode, $errorLineNumber = false, $errorFieldName = false, $errorArgument = false)
    {
        $this->errorCode = $errorCode;
        $this->errorLocationLineNumber = $errorLineNumber;
        $this->errorFieldName = $errorFieldName;
        $this->errorArgument = $errorArgument;
    }

    public function setPriceAdjustment($priceAdjustment)
    {
        $this->priceAdjustment = $priceAdjustment;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function disableFirstRowImport()
    {
        $this->importFirstRow = false;
    }

    public function setImportOrigin($importOrigin)
    {
        $this->importOrigin = $importOrigin;
    }

    public function getErrorLocationLineNumber()
    {
        return $this->errorLocationLineNumber;
    }

    public function getErrorCode()
    {
        return $this->errorCode ? $this->errorCode : "bad_file";
    }

    public function getErrorFieldName()
    {
        return $this->errorFieldName;
    }

    /**
     * @return boolean|int
     */
    public function getErrorArgument()
    {
        return $this->errorArgument;
    }
}
