<?php

class SocialPostFormStructure extends ElementForm
{
    protected $formClass = 'socialpost_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'message' => [
            'type' => 'input.html',
        ],
        'linkTitle' => [
            'type' => 'input.text',
            'textClass' => 'socialpost_linktitle',
        ],
        'linkURL' => [
            'type' => 'input.text',
            'textClass' => 'socialpost_linkurl',
        ],
        'linkDescription' => [
            'type' => 'input.html',
            'class' => 'socialpost_linkdescription',
        ],
        'image' => [
            'type' => 'input.image',
            'imageClass' => 'socialpost_currentimage',
        ],
    ];


    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('admin');
    }

    public function getFormComponents()
    {
        $structure = [];
        $structure['search'] = [
            'type' => 'ajaxsearch',
            'class' => 'socialpost_search',
            'property' => 'connectedMenu',
            'types' => $this->getSearchTypes(),
        ];
        return $structure + $this->structure;
    }
}