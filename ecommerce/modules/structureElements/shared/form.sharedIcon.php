<?php

class SharedIconStructure extends ElementForm
{
    protected $containerClass = 'gallery_form';
    protected $formClass = 'gallery_form_upload';
    protected $preset = '';
    protected $structure = [
        'connectedIcons' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'iconsList',
        ],
        'addIcon'  => [
            'type'             => 'show.heading',
            'translationGrupp' => 'shared'
        ],
        'image' => [
            'type' => 'input.dragAndDropImage'
        ]
    ];

    public function getTranslationGroup()
    {
        return 'gallery';
    }
}