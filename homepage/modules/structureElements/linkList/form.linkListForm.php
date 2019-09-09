<?php

class LinkListFormStructure extends ElementForm
{
    protected $formClass = 'linklist_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hideTitle' => [
            'type' => 'input.checkbox'
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'image' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

    protected $additionalContent = 'shared.contentlist.tpl';

    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('admin');
    }

    public function getFormComponents()
    {
        $structure = [];
        $structure['fixedId'] = [
            'type' => 'ajaxsearch',
            'class' => 'linklist_form_search',
            'property' => 'connectedMenu',
            'types' => $this->getSearchTypes(),
        ];
        return $structure + $this->structure;
    }
}