<?php

class TabsWidgetFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
    ];

    protected $additionalContent = 'component.block.list_content.tpl';
}