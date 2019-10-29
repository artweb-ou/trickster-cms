<?php

class subArticleElement extends structureElement
{
    public $dataResourceName = 'module_article';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $multiLanguage = false;


    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        if($parentElement = $this->getCurrentParentElement()) {
            if ($parentElement->structureType == 'product') {
                $this->multiLanguage = true;
                $multiLanguageFields[] = 'title';
                $multiLanguageFields[] = 'content';
            }
        }
    }

    public function getFormStructure()
    {
        if ($this->multiLanguage) {
            return [
                'title'   => [
                    'type' => 'input.multi_language_text',
                ],
                'content' => [
                    'type'  => 'input.multi_language_content',
                    'class' => '',
                    'style' => ''
                ],
                'image'   => [
                    'type'     => 'input.image',
                    'preset'   => 'adminImage',
                    'filename' => 'image',
                ],
            ];
        } else {
            return [
                'title' => [
                    'type' => 'input.text',
                ],
                'content' => [
                    'type' => 'input.html',
                ],
                'image' => [
                    'type' => 'input.image',
                    'preset' => 'adminImage',
                    'filename' => 'image',
                ],
            ];
        }
    }
}