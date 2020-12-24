<?php

class productParameterElement extends structureElement
{
    public $dataResourceName = 'module_product_parameter';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $value = false;
    protected $parameterGroup;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hint'] = 'html';
        $moduleStructure['single'] = 'checkbox';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['primary'] = 'text';

        $moduleStructure['categoriesIds'] = 'numbersArray';
        $moduleStructure['importInfo'] = 'array';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'hint';
    }

    public function hasHints()
    {
        return $this->hint != false;
    }

    public function getParameterGroup()
    {
        if (is_null($this->parameterGroup)) {
            $structureManager = $this->getService('structureManager');
            $this->parameterGroup = $structureManager->getElementsFirstParent($this->id);
        }
        return $this->parameterGroup;
    }

    public function getConnectedCategoriesIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'categoryParameter', 'child');
    }

    public function deleteElementData()
    {
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as $record) {
            $record->delete();
        }
        $collection = persistableCollection::getInstance('module_product_parameter_value');
        $searchFields = ['parameterId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as $record) {
            $record->delete();
        }
        parent::deleteElementData();
    }
}