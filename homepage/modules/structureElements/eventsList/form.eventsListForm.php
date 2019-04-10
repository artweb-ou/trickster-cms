<?php

class EventsListFormStructure extends ElementForm
{
    protected $formClass = 'eventslist_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'sort' => [
            'type' => 'select.index',
            'options' => [
                'asc' => 'sort_asc',
                'desc' => 'sort_desc',
            ],
        ],
        'mode' => [
            'type' => 'select.index',
            'options' => [
                'auto' => 'mode_auto',
                'custom' => 'mode_custom',
            ],
        ],
        'receivedEventsIds' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'eventslist_connected_events_select',
            'name' => 'receivedEventsIds',
            'method' => 'getConnectedEvents',
        ],
        'dates_type' => [
            'type' => 'select.index',
            'options' => [
                'no_filter' => 'dates_no_filter',
                'past_events' => 'dates_past_events',
                'future_events' => 'dates_future_events',
            ],
        ],
        'date_from' => [
            'type' => 'input.date',
        ],
        'date_to' => [
            'type' => 'input.date',
        ],
        'enableFilter' => [
            'type' => 'input.checkbox',
        ],
        'structureRole' => [
            'type' => 'select.array',
            'options' => [
                'hybrid',
                'content',
                'container',
            ],
            'translationGroup' => 'menulogic',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}
