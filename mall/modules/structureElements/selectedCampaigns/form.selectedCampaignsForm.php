<?php

class SelectedCampaignsFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'connectAll' => [
            'type' => 'input.checkbox',
        ],
        'receivedCampaignsIds' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getAvailableCampaigns',
            'class' => '',
        ],
    ];
    protected $controls = 'controls';
}