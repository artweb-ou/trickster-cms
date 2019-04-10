<?php

class FeedbackFormStructure extends ElementForm
{
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
    ];

    protected $additionalContent = 'shared.contentlist';
}
