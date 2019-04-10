<?php

class productParametersGroupElement extends structureElement
{
    public $dataResourceName = 'module_product_parameters_group';
    protected $allowedTypes = [
        'productParameter',
        'productSelection',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';
    public $parametersList;

    // TODO: refactoring
    public function getParametersList($skipOptions = false)
    {
        $parametersList = [];
        if (is_null($this->parametersList)) {
            $this->parametersList = $this->getService('structureManager')->getElementsChildren($this->id);
        }
        if ($skipOptions && $this->parametersList) {
            foreach ($this->parametersList as &$parameter) {
                if (!$parameter->option) {
                    $parametersList[] = $parameter;
                }
            }
        } else {
            $parametersList = $this->parametersList;
        }
        return $parametersList;
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showImportForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['isMinimized'] = 'text';
        $moduleStructure['importInfo'] = 'array';
        $moduleStructure['hidden'] = 'checkbox';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function deleteElementData()
    {
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        parent::deleteElementData();
    }
}