<?php

interface ImageUrlProviderInterface
{
    public function getImageId($mobile = false);

    public function getImageName($mobile = false);

    public function getImageUrl($preset = 'adminImage', $mobile = false);
}