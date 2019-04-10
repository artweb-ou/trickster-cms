<?php

class PollQuestionFormStructure extends ElementForm
{
    protected $structure = [
        'questionText' => [
            'type' => 'input.multi_language_text',
        ],
        'multiChoice' => [
            'type' => 'input.checkbox',
        ],
    ];

    protected $additionalContent = 'shared.contentlist_singlepage';
}