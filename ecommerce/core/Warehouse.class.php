<?php

/**
 * Base class for retrieving products and related elements from external sources
 */
abstract class Warehouse extends errorLogger
{
    const CODE = 'define me!';
    protected $tempDirectory = '';

    /**
     * @return WarehouseCategory[]
     */
    public abstract function getCategories();

    /**
     * @param $categoryCode
     * @return WarehouseProduct[]
     */
    public abstract function getProductsByCategory($categoryCode);

    /**
     * @param $productCode
     * @return WarehouseParameter[]
     */
    public abstract function getProductParameters($productCode);

    public function getTempDirectory()
    {
        return $this->tempDirectory;
    }

    public function setTempDirectory($directory)
    {
        $this->tempDirectory = (string)$directory;
    }
}

abstract class WarehouseEntity
{
    public $code = ''; // entity identifier in certain warehouse
    /**
     * @var Warehouse
     */
    protected $warehouse;

    public function __construct(Warehouse $warehouse, $code)
    {
        $this->code = $code;
        $this->warehouse = $warehouse;
    }
}

class WarehouseProduct extends WarehouseEntity
{
    public $title = '';
    public $price = 0.000000;
    public $quantity = 0;
    public $warranty = 0; // in months!
    public $vendorCode = '';
    public $manufacturerCode = ''; // vendor assigned identifier
    public $rrp = 0.000000; // recommended price
    public $description = '';
    public $barcode = ''; // EAN-13 barcode
    public $dateExpected = ''; // date.month.year
    /**@var WarehouseResource[] */
    protected $images = [];

    /**
     * @return WarehouseResource[]
     */
    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return WarehouseParameter[]
     */
    public function getParameters()
    {
        return $this->warehouse->getProductParameters($this->code);
    }

    public function getVendorName()
    {
        return $this->vendorCode;
    }
}

class WarehouseParameter extends WarehouseEntity
{
    public $title = '';
    public $value = '';
    public $description = '';
    public $redundant = false; // true if this parameter is not really parameter, but used for any kind of properties
}

class WarehouseCategory extends WarehouseEntity
{
    public $title = '';
    public $parentCode = '';
    /**
     * @var WarehouseCategory[]
     */
    public $children = [];
}

/**
 * Resource, like a product image.
 */
class WarehouseResource extends errorLogger
{
    protected $warehouse;
    protected $url = '';
    protected $name = '';
    protected $content = '';
    protected $localPath;

    public function __construct(Warehouse $warehouse, $name = '')
    {
        $this->warehouse = $warehouse;
        $this->name = $name;
    }

    /**
     * Set file name based on URL
     * Should be used only when URL points to the file (like //example.com/file.ext),
     * not when it points to script (//example.com/dl.php?get=file.ext)
     */
    public function resolveNameFromUrl()
    {
        if ($this->url) {
            $urlinfo = parse_url($this->url);
            if ($urlinfo) {
                $pathinfo = pathinfo($urlinfo['path']);
                if ($pathinfo) {
                    $this->name = $pathinfo['basename'];
                }
            }
            if (!$this->name) {
                $this->logError('Failed to resolve image name from URL (' . $this->url . ')');
            }
        } else {
            $this->logError('Attempt to resolve image from empty URL');
        }
    }

    protected function download()
    {
        $this->content = file_get_contents($this->url);
    }

    protected function createLocalFile()
    {
        $directory = $this->warehouse->getTempDirectory();
        $fileName = $this->name ?: uniqid();
        $fileCreated = file_put_contents($directory . $fileName, $this->content);
        if ($fileCreated) {
            $this->localPath = $directory . $fileName;
        } else {
            $this->logError('Failed to create resource file at ' . $directory . $fileName);
        }
    }

    public function getLocalPath()
    {
        if ($this->localPath === null) {
            $this->localPath = '';
            if ($this->url !== '') {
                $this->download();
            }
            if ($this->content !== '') {
                $this->createLocalFile();
            }
        }
        return $this->localPath;
    }

    public function deleteLocalFile()
    {
        if (is_file($this->localPath)) {
            unlink($this->localPath);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($newName)
    {
        $this->name = $newName;
    }

    public function setContent($newContent)
    {
        $this->content = $newContent;
    }

    public function setUrl($newUrl)
    {
        $this->url = $newUrl;
    }

    public function getUrl()
    {
        return $this->url;
    }
}

interface MinimumActiveProductsInfoProvider
{
    public function getMinimumActiveProductsInfo(array $relevantIds);
}

interface WarehouseCategoriesTreeProvider
{
    public function getCategoriesTree();
}