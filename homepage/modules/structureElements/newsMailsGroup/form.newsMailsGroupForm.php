<?php

class NewsMailsGroupFormStructure extends ElementForm
{
    protected $formClass = 'newsmailsgroup_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'marker' => [
            'type' => 'select.index',
            'options' => [
                'newsmail_subscribed' => 'marker_subscribed',
                'newsmail_registered' => 'marker_registered',
            ],
        ],
        'addAddresses' => [
            'type' => 'select.universal_options_multiple',
            'class' => 'newsmailsgroup_address_select',
        ],
    ];

    protected $additionalContent = 'shared.contentlist.tpl';
}