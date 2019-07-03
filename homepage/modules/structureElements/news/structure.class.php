<?php

class newsElement extends structureElement implements MetadataProviderInterface, CommentsHolderInterface, ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;
    use MetadataProviderTrait {
        getTextContent as getTextContentTrait;
    }
    use CommentsTrait;

    public $dataResourceName = 'module_news';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->getTextContentTrait();
            $this->textContent .= " " . $this->date;
        }
        return $this->textContent;
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['date'] = 'date';
        $moduleStructure['content'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['formRelativesInput'] = 'array';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
            'showLanguageForm',
        ];
    }

    public function getParent()
    {
        return $this->getService('structureManager')->getElementsFirstParent($this->id);
    }
}