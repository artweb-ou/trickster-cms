<?php

class ImportCalculationsRuleFormStructure extends ElementForm
{
    protected $formClass = 'importcalculationsrule_form';
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'rules' => [
            'type' => 'input.rules',
        ],
        'action' => [
            'type' => 'select.array',
            'options' => ['modify', 'use_rrp'],
        ],
        'priceModifier' => [
            'type' => 'input.text',
        ],
    ];

}