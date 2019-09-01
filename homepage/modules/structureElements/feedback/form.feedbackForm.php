<?php

class FeedbackFormStructure extends ElementForm
{
    protected $formClass = 'feedback_form';
    protected $containerClass = 'feedback_container';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'destination' => [
            'type' => 'input.email',
        ],
        'buttonTitle' => [
            'type' => 'input.text',
        ],
        'content' => [
            'type' => 'input.html',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

    protected $additionalContent = 'shared.contentlist.tpl';
}
