<?php

class productGalleryElement extends menuDependantStructureElement
{
    use ConfigurableLayoutsProviderTrait;

    public $dataResourceName = 'module_productgallery';
    protected $allowedTypes = ['productGalleryImage'];
    public $defaultActionName = 'show';
    public $role = 'hybrid';
    protected $galleryData;
    protected $connectedProducts = array();

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['icon'] = 'image';
        $moduleStructure['iconOriginalName'] = 'text';
        $moduleStructure['popup'] = 'checkbox';
        $moduleStructure['showConnectedProducts'] = 'checkbox';
        $moduleStructure['productsLayout'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showLayoutForm',
            'showImages',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getGalleryInfo($options = [])
    {
        $controller = controller::getInstance();

        $this->galleryData = [];
        $this->galleryData['id'] = $this->id;
        $this->galleryData['title'] = $this->title;
        $this->galleryData['description'] = $this->description;
        $this->galleryData['images'] = [];
        $this->galleryData['popup'] = $this->popup;

        if (isset($options['popupPositioning'])) {
            $this->galleryData['popupPositioning'] = $options['popupPositioning'];
        } else {
            $this->galleryData['popupPositioning'] = 'center';
        }

        if (isset($options['staticDescriptionEnabled'])) {
            $this->galleryData['staticDescriptionEnabled'] = $options['staticDescriptionEnabled'];
        } else {
            $this->galleryData['staticDescriptionEnabled'] = false;
        }
        if (isset($options['imageDescriptionEnabled'])) {
            $this->galleryData['imageDescriptionEnabled'] = $options['imageDescriptionEnabled'];
        } else {
            $this->galleryData['imageDescriptionEnabled'] = true;
        }
        if (isset($options['heightLogics'])) {
            $this->galleryData['heightLogics'] = $options['heightLogics'];
        } else {
            $this->galleryData['heightLogics'] = 'containerHeight';
        }
        if (isset($options['height'])) {
            $this->galleryData['height'] = $options['height'];
        } else {
            $this->galleryData['height'] = 0.5;
        }

        $structureManager = $this->getService('structureManager');
        $headerGalleryImages = $structureManager->getElementsChildren($this->id);

        foreach ($headerGalleryImages as &$imageElement) {
            $imageInfo = [];
            $imageInfo['id'] = $imageElement->id;
            $imageInfo['title'] = $imageElement->title;
            $imageInfo['description'] = $imageElement->description;
            $imageInfo['labelText'] = $imageElement->labelText;
            $imageInfo['link'] = $imageElement->link;
            $imageInfo['image'] = $controller->baseURL . 'image/type:productGallery/id:' . $imageElement->image . '/filename:' . $imageElement->originalName;
            $imageInfo['placeMarks'] = [];

            $placeMarks = $structureManager->getElementsChildren($imageElement->id);
            foreach ($placeMarks as &$placeMark) {
                $placeMarkInfo = [];
                $placeMarkInfo['id'] = $placeMark->productId;
                $placeMarkInfo['positionX'] = $placeMark->positionX;
                $placeMarkInfo['positionY'] = $placeMark->positionY;
                $placeMarkInfo['products'] = [];
                if (!empty($placeMark->title)) {
                    $productInfo = [];
                    $productInfo['title'] = $placeMark->title;
                    $productInfo['code'] = $placeMark->code;
                    $productInfo['description'] = $placeMark->description;
                    $productInfo['price'] = $placeMark->price;
                    $productInfo['image'] = $controller->baseURL . 'image/type:productGalleryProduct/id:' . $placeMark->image;
                    $placeMarkInfo['products'][] = $productInfo;
                    $placeMark->URL = null;
                    $this->connectedProducts[] = $placeMark;
                } else {
                    foreach ($placeMark->getConnectedProducts() as $productElement) {
                        $productInfo = [];
                        $productInfo['title'] = $productElement->title;
                        if ($productElement->introduction) {
                            $productInfo['description'] = $productElement->introduction;
                        } else {
                            $productInfo['description'] = $productElement->content;
                        }
                        $productInfo['price'] = $productElement->price;
                        $productInfo['image'] = $controller->baseURL . 'image/type:productGalleryProduct/id:' . $productElement->image . '/filename:' . $productElement->originalName;
                        $productInfo['url'] = $productElement->URL;
                        $productInfo['primaryParametersInfo'] = $productElement->getPrimaryParametersInfo();
                        $placeMarkInfo['products'][] = $productInfo;
                        if (!array_key_exists($productElement->id, $this->connectedProducts)) {
                            $this->connectedProducts[$productElement->id] = $productElement;
                        }

                    }
                }
                $imageInfo['placeMarks'][] = $placeMarkInfo;
            }
            $this->galleryData['images'][] = $imageInfo;
        }
        $this->galleryData = json_encode($this->galleryData);
        return $this->galleryData;
    }

    /**
     * @return array
     */
    public function getConnectedProducts()
    {
        return $this->connectedProducts;
    }

    public function getParentUrl()
    {
        $parent = $this->getCurrentParentElement();
        $url = $parent->URL;
        return $url;
    }
}