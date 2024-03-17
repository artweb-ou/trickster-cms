<?php

trait ImageUrlProviderTrait
{
    public function getImageId($mobile = false)
    {
        if ($mobile) {
            return $this->mobileImage;
        }
        return $this->image;
    }

    public function getImageName($mobile = false)
    {
        if ($mobile) {
            return $this->mobileImageName;
        }
        return $this->originalName;
    }

    public function getImageUrl(string $preset = 'adminImage', bool $mobile = false): ?string
    {
        $controller = $this->getService('controller');
        if ($mobile && ($imageId = $this->getImageId($mobile))) {
            $url = $controller->baseURL . 'image/type:' . $preset . '/id:' . $imageId . '/filename:' . $this->getImageName(true);
        } else {
            $url = $controller->baseURL . 'image/type:' . $preset . '/id:' . $this->getImageId() . '/filename:' . $this->getImageName();
        }
        return $url;
    }
}