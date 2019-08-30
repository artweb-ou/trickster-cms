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
        'needSaveCurrentUrl' => [
            'type' => 'input.checkbox',
        ],
        'displayMenus' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getDisplayMenusInfo',
            'condition' => 'checkDisplayMenus',
        ],
    ];

    protected $structureHiddenElements = [
        'currentUrl' => [ // $_SERVER['REQUEST_URI']
            'type' => 'input.text',
            'inputType' => 'hidden',
            'method'  => 'getCurrentURL',
        ],
    ];

    public function getFormComponents()
    {
        $needSaveCurrentUrl = $this->getElementProperty('needSaveCurrentUrl');
        $needSaveCurrentUrlDependentStructureElements = [];
        switch ($needSaveCurrentUrl) {
            /*
            0
            1
            */
            case '1': //  need
                $needSaveCurrentUrlDependentStructureElements = $this->structureHiddenElements;
                break;

            default:
                break;
        }

        return $this->structure +
            $needSaveCurrentUrlDependentStructureElements;
    }

    protected $additionalContent = 'shared.contentlist.tpl';
}
