<?php

class NewsMailsTextFormStructure extends ElementForm
{
    protected $formClass = 'newsmailinfo_block';
    protected $structure = [
        'from' => [
            'type' => 'input.text',
        ],
        'fromEmail' => [
            'type' => 'input.text',
        ],
        'title' => [
            'type' => 'input.text',
            'textClass' => 'newsmailinfo_import_title',
        ],
        'customTemplate' => [
            'type' => 'select.array',
            'method' => 'getTemplateOptions',
            'defaultRequired' => true,
            'defaultName' => 'e-News'
        ],
        'selectedEmails' => [

        ],
        'content' => [
            'type' => 'input.html',
            'class' => 'newsmailinfo_content',
        ],
    ];

    protected $additionalContent = 'component.block.news_mails_text.tpl';

    protected function getSearchTypes()
    {
        return $this->element->getSearchTypesString('public');
    }

    public function getFormComponents()
    {
        $structure = [
            'type' => 'newsmailstext_ajaxsearch',
            'class' => 'newsmailinfo_search',
            'property' => 'newsmailinfo',
            'types' => $this->getSearchTypes(),
            'import_class' => 'newsmailinfo',
        ];
        $this->structure['selectedEmails'] = $structure;
        return $this->structure;
    }
}