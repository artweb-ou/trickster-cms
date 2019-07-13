<?php

class articleElement extends menuDependantStructureElement
{
    use CommentsTrait;
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_article';
    protected $allowedTypes = ['subArticle'];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['subLayout'] = 'text';
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

    public function getSubArticles()
    {
        /**
         * @var structureManager $structureManager
         */
        $structureManager = $this->getService('structureManager');
        $subArticles = $structureManager->getElementsChildren($this->id);
        return $subArticles;
    }
}

