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
            'class' => 'selectedevents_mode_select',
            'options' => [
                'auto' => 'mode_auto',
                'custom' => 'mode_custom',
            ],
        ],
        'receivedEventsListsIds' => [
            'type' => 'select.universal_options_multiple',
            'trClass' => 'selectedevents_manual_setting',
            'class' => 'selectedevents_connected_eventslists_select',
            'method' => 'getConnectedEventsListsInfo',
        ],
        'receivedEventsIds' => [
            'type' => 'select.universal_options_multiple',
            'trClass' => 'selectedevents_manual_setting',
            'class' => 'selectedevents_connected_events_select',
            'method' => 'getConnectedEventsInfo',
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

    protected $additionalContent = 'shared.contentlist.tpl';

    protected function getSearchTypes()
    {
//        return $this->element->getSearchTypesString('admin');
        return "folder,news,gallery,newsList,production,service,Event,EventsList";
    }

    public function getFormComponents()
    {
        $structure = [];
        $structure['fixedId'] = [
            'type' => 'ajaxsearch',
            'class' => 'selectedevents_form_search',
            'property' => 'connectedMenu',
            'types' => $this->getSearchTypes(),
        ];
        return  $this->structure + $structure;
    }

}
