<?php

class newsMailTextSubContentElement extends structureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_newsmailtext_subcontent';
    protected $allowedTypes = [];
    public $defaultActionName = 'showForm';
    public $role = 'content';
    const LINK_TYPE_CATEGORY = 'newsMailTextSubContentCategory';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['content'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['link'] = 'url';
        $moduleStructure['linkName'] = 'text';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['title'] = 'text';
        $moduleStructure['categoryInput'] = 'text';
        $moduleStructure['replacementImage'] = 'text';
        $moduleStructure['contentStructureType'] = 'text';
        $moduleStructure['field1'] = 'text';
        $moduleStructure['field2'] = 'text';
        $moduleStructure['field3'] = 'text';
    }

    public function getConnectedCategory()
    {
        $linksManager = $this->getService('linksManager');
        $connectedCategoriesIds = $linksManager->getConnectedIdList($this->id, self::LINK_TYPE_CATEGORY, 'child');
        if ($connectedCategoriesIds) {
            $structureManager = $this->getService('structureManager');
            return $structureManager->getElementById($connectedCategoriesIds[0]);
        }
        return false;
    }

    public function getCategoryCode()
    {
        return ($category = $this->getConnectedCategory()) ? $category->code : '';
    }
}