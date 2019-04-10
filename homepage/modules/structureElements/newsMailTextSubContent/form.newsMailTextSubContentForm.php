<?php

class NewsMailTextSubContentFormStructure extends ElementForm
{
    protected $formClass = 'newsmailtextsubcontent_form';
    protected $structure = [
        'search'               => [

        ],
        'categoryInput'        => [
            'type'            => 'select.element',
            'property'        => 'subContentCategories',
            'defaultRequired' => true,
        ],
        'title'                => [
            'type'      => 'input.text',
            'textClass' => 'newsmailtextsubcontent_import_infotitle',
        ],
        'content'              => [
            'type'  => 'input.html',
            'class' => 'newsmailtextsubcontent_import_content_info',
        ],
        'image'                => [
            'type'       => 'input.image',
            'imageClass' => 'newsmailtextsubcontent_form_currentimage',
        ],
        'link'                 => [
            'type'      => 'input.text',
            'textClass' => 'newsmailtextsubcontent_import_link',
        ],
        'linkName'             => [
            'type' => 'input.text',
        ],
        'contentStructureType' => [
            'type'      => 'input.text',
            'textClass' => 'newsmailtextsubcontent_import_structure_type',
        ],
        'field1'               => [
            'type' => 'input.text',
        ],
        'field2'               => [
            'type' => 'input.text',
        ],
        'field3'               => [
            'type' => 'input.text',
        ],
    ];


    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('public');
    }

    public function getFormComponents()
    {
        $structure = [
            'type'         => 'newsmailstext_ajaxsearch',
            'class'        => 'newsmailtextsubcontent_form_search',
            'types'        => $this->getSearchTypes(),
            'import_class' => 'newsmailtextsubcontent',
        ];
        $this->structure['search'] = $structure;
        return $this->structure;
    }
}