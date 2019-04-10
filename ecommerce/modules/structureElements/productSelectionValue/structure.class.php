<?php

class productSelectionValueElement extends structureElement
{
    public $dataResourceName = 'module_product_selection_value';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $selectionElement;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['importKeywords'] = 'text';
        $moduleStructure['excludeImportKeywords'] = 'text';
        $moduleStructure['value'] = 'text';
        $moduleStructure['hint'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['importInfo'] = 'array';
        // tmp
        $moduleStructure['mergeIds'] = 'array';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'hint';
    }

    public function getParentElement()
    {
        return $this->getService('structureManager')->getElementsFirstParent($this->id);
    }

    public function getSelectionElement()
    {
        if (is_null($this->selectionElement)) {
            $structureManager = $this->getService('structureManager');
            $this->selectionElement = $structureManager->getElementsFirstParent($this->id);
        }
        return $this->selectionElement;
    }

    public function getValueRecords()
    {
        $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
        $selectionElement = $this->getSelectionElement();
        $records = $valuesCollection->load(
            [
                'parameterId' => $selectionElement->id,
                'value' => $this->id,
            ]
        );
        return $records;
    }

    public function getConnectedProductsIds()
    {
        $productsIds = [];
        $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
        $selectionElement = $this->getSelectionElement();
        $conditions = [
            [
                'column' => 'parameterId',
                'action' => '=',
                'argument' => $selectionElement->id,
            ],
            [
                'column' => 'value',
                'action' => '=',
                'argument' => $this->id,
            ],
        ];
        if ($valuesList = $valuesCollection->conditionalLoad(['productId'], $conditions)) {
            foreach ($valuesList as &$value) {
                $productsIds[] = $value['productId'];
            }
        }
        return $productsIds;
    }

    public function deleteElementData()
    {
        $collection = persistableCollection::getInstance('module_product_parameter_value');
        $searchFields = [
            'parameterId' => $this->getParentElement()->id,
            'value' => $this->id,
        ];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        $collection = persistableCollection::getInstance('import_origin');
        $searchFields = ['elementId' => $this->id];
        $records = $collection->load($searchFields);
        foreach ($records as &$record) {
            $record->delete();
        }
        $productOptionsImagesManager = $this->getService('ProductOptionsImagesManager');
        $query = $productOptionsImagesManager->queryDb();
        $query->where('selectionValue', '=', $this->id);
        $query->delete();
        parent::deleteElementData();
    }
}

