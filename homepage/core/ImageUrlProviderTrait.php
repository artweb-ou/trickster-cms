<?php

trait ImageUrlProviderTrait
{

    public function getImageId()
    {
        return $this->image;
    }

    public function getImageName()
    {
        return $this->originalName;
    }

    public function getImageUrl($preset = 'adminImage')
    {
        $controller = $this->getService('controller');
        $url = $controller->baseURL . 'image/type:' . $preset . '/id:' . $this->getImageId() . '/filename:' . $this->getImageName();
        return $url;
    }

    public function image_exists($url)
    {
        $headers = get_headers($url);
        return stripos($headers[0], "200 OK") ? true : false;
    }
}