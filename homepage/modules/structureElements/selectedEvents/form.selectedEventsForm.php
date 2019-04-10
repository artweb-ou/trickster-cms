<?php

class SelectedEventsFormStructure extends ElementForm
{
    protected $formClass = 'selectedevents_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'mode' => [
            'type' => 'select.index',
            'options' => [
                'auto' => 'mode_auto',
                'custom' => 'mode_custom',
            ],
        ],
        'receivedEventsListsIds' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'selectedevents_connected_eventslists_select',
            'method' => 'getConnectedEventsLists',
        ],
        'receivedEventsIds' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'selectedevents_connected_events_select',
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
        'sort' => [
            'type' => 'select.index',
            'options' => [
                'asc' => 'sort_asc',
                'desc' => 'sort_desc',
            ],
        ],
        'displayLimit' => [
            'type' => 'input.text',
        ],
        'enableFilter' => [
            'type' => 'input.checkbox',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

}
