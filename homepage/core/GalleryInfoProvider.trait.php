<?php

trait GalleryInfoProviderTrait
{
    public function getGalleryJsonInfo($galleryOptions = [], $imagePresetBase = 'gallery')
    {
        $galleryData = [
            'id' => $this->id,
            'galleryResizeType' => 'viewport',
            'galleryWidth' => false,
            'galleryHeight' => false,
            'imageResizeType' => 'resize',
            'changeDelay' => 6000,
            'imageAspectRatio' => 0.8,
            'thumbnailsSelectorEnabled' => true,
            'thumbnailsSelectorHeight' => '15%',
            'imagesButtonsEnabled' => false,
            'playbackButtonEnabled' => false,
            'imagesPrevNextButtonsEnabled' => false,
            'fullScreenGalleryEnabled' => true,
            'fullScreenButtonEnabled' => false,
            'descriptionType' => 'overlay',
            'imagesPrevNextButtonsSeparated' => false,
        ];
        $galleryData = array_merge($galleryData, $galleryOptions);

        $galleryData['images'] = [];
        $controller = controller::getInstance();
        foreach ($this->getImagesList() as &$imageElement) {
            if ($imageElement instanceof ImageUrlProviderInterface) {
                $imageId = $imageElement->getImageId();
                $imageName = $imageElement->getImageName();
            } else {
                $imageId = $imageElement->image;
                $imageName = $imageElement->originalName;
            }

            $galleryData['images'][] = [
                'fullImageUrl' => $controller->baseURL . 'image/type:' . $imagePresetBase . 'FullImage/id:' . $imageId . '/filename:' . $imageName,
                'bigImageUrl' => $controller->baseURL . 'image/type:' . $imagePresetBase . 'Image/id:' . $imageId . '/filename:' . $imageName,
                'thumbnailImageUrl' => $controller->baseURL . 'image/type:' . $imagePresetBase . 'SmallThumbnailImage/id:' . $imageId . '/filename:' . $imageName,
                'title' => $imageElement->title,
                'description' => $imageElement->description,
                'alt' => $imageElement->alt,
                'link' => $imageElement->link,
                'externalLink' => $imageElement->externalLink,
                'id' => $imageElement->id,
            ];
        }
        return json_encode($galleryData);
    }

    abstract public function getImagesList();
}