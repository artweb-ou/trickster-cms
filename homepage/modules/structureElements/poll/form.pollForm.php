<?php

class PollFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'description' => [
            'type' => 'input.multi_language_text',
        ],
    ];

    protected $additionalContent = 'component.block.poll_extra_content';
}