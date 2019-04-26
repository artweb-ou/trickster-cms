<?php

class ProductIconStructure extends ElementForm
{
    protected $formClass = 'gallery_form';
    protected $preset = '';
    protected $additionalContent = 'component.show.icon_form';

    public function getTranslationGroup()
    {
        return 'gallery';
    }
}