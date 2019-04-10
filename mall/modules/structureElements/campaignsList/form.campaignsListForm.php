<?php

class CampaignsListFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'columns' => [
            'type' => 'select.universal_options',
            'options' => ['left', 'right', 'none', 'both'],
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'connectAll' => [
            'type' => 'input.checkbox',
        ],
        'campaigns' => [
            'type' => 'select.universal_options_multiple',
            'property' => 'campaignsList',
            'class' => 'campaignslist_campaigns_select',

        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
    ];
    protected $controls = 'controls';
}