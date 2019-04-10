<?php

class OpeningHoursExceptionFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'startTime' => [
            'type' => 'input.text',
        ],
        'endTime' => [
            'type' => 'input.text',
        ],
        'closed' => [
            'type' => 'input.checkbox',
        ],
    ];
    protected $controls = 'controls';
}