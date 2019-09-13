<?php

class FormFieldsGroupFormStructure extends ElementForm
{
    protected $formClass = 'fieldsgroup_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hideTitle' => [
            'type' => 'input.checkbox',
            'translationGroup' => 'shared',
        ],
    ];

    protected $additionalContent = 'shared.contentlist.tpl';
}