<?php

class SharedIconStructure extends ElementForm
{
    protected $containerClass = 'gallery_form';
    protected $formClass = 'gallery_form_upload';
    protected $preset = '';
    protected $controlsLayout = null;
    protected $structure = [
        'image' => [
            'type' => 'input.dragAndDropImage'
        ]
    ];
    protected $controls = false;
    protected $additionalContent = 'shared.contentlist.tpl';
    protected $additionalContentTable = 'shared.contenlistGalleryImage.tpl';

    public function getTranslationGroup()
    {
        return 'gallery';
    }
}