<?php

class OpeningHoursGroupFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.multi_language_text',
        ],
        'open_hours' => [
            'type' => 'input.opening_hours_form_section',
        ],
    ];
    protected $controls = 'controls';
    protected $additionalContent = 'shared.contentlist_singlepage';
}