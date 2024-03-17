<?php

interface ImageUrlProviderInterface
{
    public function getImageId($mobile = false);

    public function getImageName($mobile = false);

    public function getImageUrl(string $preset = 'adminImage', bool $mobile = false): ?string;

}