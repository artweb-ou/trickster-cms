<?php

interface ImageUrlProviderInterface
{
    public function getImageId();

    public function getImageName();

    public function getImageUrl($preset = 'adminImage');
}