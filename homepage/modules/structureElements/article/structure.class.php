<?php

class articleElement extends menuDependantStructureElement
{
    use CommentsTrait;
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_article';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['mobileLayout'] = 'text';
        $moduleStructure['allowComments'] = 'checkbox';
    }

    public function getCommentFormActionURL()
    {
        $structureManager = $this->getService('structureManager');
        if ($parent = $structureManager->getElementsFirstParent($this->id)) {
            return $parent->getFormActionURL();
        }
        return false;
    }
}

