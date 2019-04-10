<?php

class FloorFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.image',
        ],
        'map' => [
            'type' => 'show.map_editor',
        ],
    ];
    protected $controls = 'controls';
    protected $additionalContent = 'shared.contentlist_singlepage';
}