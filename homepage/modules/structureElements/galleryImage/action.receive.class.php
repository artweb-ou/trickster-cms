<?php

class receiveGalleryImage extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param galleryImageElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }

            if ($structureElement->getDataChunk("mobileImage")->originalName) {
                $structureElement->mobileImage = $structureElement->id.'_mobile';
                $structureElement->mobileImageName = $structureElement->getDataChunk("mobileImage")->originalName;
            }

            if ($structureElement->alt == '') {
                if ($structureElement->title == '') {
                    $info = pathinfo($structureElement->getDataChunk("image")->originalName);
                    $structureElement->alt = $info['filename'];
                } else {
                    $structureElement->alt = $structureElement->title;
                }
            }

            $structureElement->persistElementData();
        }
        if ($controller->getApplicationName() != 'adminAjax') {
            $controller->redirect($structureElement->URL);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'mobileImage',
            'alt',
            'title',
            'description',
            'externalLink',
        ];
    }
}