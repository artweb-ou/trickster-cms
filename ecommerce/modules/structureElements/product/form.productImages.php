<?php

class ProductImagesStructure extends ElementForm
{
    protected $containerClass = 'gallery_form';
    protected $formClass = 'gallery_form_upload';
    protected $preset = '';
    protected $structure = [
        'image' => ['type' => 'input.dragAndDropImage']
    ];
    protected $additionalContent = 'shared.contentImagesTable';
    public function getTranslationGroup()
    {
        return 'gallery';
    }
}