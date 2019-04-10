<?php

/**
 * Class ImagesElementTrait
 *
 * @property array imagesSelector
 */
trait ImagesElementTrait
{
    protected $imagesList;

    /**
     * @return galleryImageElement[]
     */
    public function getImagesList()
    {
        $structureManager = $this->getService('structureManager');
        if ($this->imagesList === null) {
            $this->imagesList = [];
            if ($childElements = $structureManager->getElementsChildren($this->id, null, $this->getImagesLinkType())) {
                foreach ($childElements as $childElement) {
                    if ($childElement->structureType == 'galleryImage') {
                        $this->imagesList[] = $childElement;
                    }
                }
            }
        }
        return $this->imagesList;
    }

    public function getImagesLinkType()
    {
        return 'connectedImage';
    }

    public function getImage($number = 0)
    {
        if ($images = $this->getImagesList()) {
            if (isset ($images[$number])) {
                return $images[$number];
            }
        }
        return false;
    }
}