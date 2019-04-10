<?php

class EventFormStructure extends ElementForm
{
    protected $formClass = 'event_form';
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'introduction' => [
            'type' => 'input.multi_language_content',
        ],
        'description' => [
            'type' => 'input.multi_language_content',
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'startTime' => [
            'type' => 'input.text',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'endTime' => [
            'type' => 'input.text',
        ],
        'country' => [
            'type' => 'input.multi_language_text',
        ],
        'city' => [
            'type' => 'input.multi_language_text',
        ],
        'address' => [
            'type' => 'input.multi_language_text',
        ],
	'location' => [
            'type' => 'input.multi_language_text',
        ],
        'image' => [
            'type' => 'input.image',
            'preset' => 'adminImage',
            'filename' => 'originalName',
        ],
        'mapCode' => [
            'type' => 'input.google_maps',
        ],
        'link' => [
            'type' => 'input.text',
        ],
        'connectedEventsLists' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'event_connected_eventslists_select',
            'method' => 'getConnectedEventsLists',
        ],
    ];

}