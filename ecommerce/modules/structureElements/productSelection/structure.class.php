<?php

/**
 * Class productSelectionElement
 *
 * @property string $title
 */
class productSelectionElement extends structureElement
{
    public $dataResourceName = 'module_product_selection';
    protected $allowedTypes = ['productSelectionValue'];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $selectedOptionId = false;
    public $productOptions = false;
    public $options = false;
    /**
     * @var productParametersGroupElement
     */
    protected $parameterGroup;
    protected $selectionOptions;
    protected $usedOptions;
    protected $connectedFilterableCategories;
    protected $connectedFilterableCategoriesIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hint'] = 'html';
        $moduleStructure['primary'] = 'text';
        $moduleStructure['code'] = 'text';
        $moduleStructure['option'] = 'checkbox';
        $moduleStructure['controlType'] = 'text';
        $moduleStructure['influential'] = 'checkbox';

        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
        $moduleStructure['paramRange'] = 'text';
        $moduleStructure['type'] = 'text';
        $moduleStructure['categoriesIds'] = 'numbersArray';
        $moduleStructure['importInfo'] = 'array';
        $moduleStructure['mergeId'] = 'text';
        $moduleStructure['formFilterableCategoriesIds'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'hint';
    }

    public function getSelectionOptions()
    {
        if ($this->selectionOptions === null) {
            $this->selectionOptions = $this->getService('structureManager')->getElementsChildren($this->id);
        }
        return $this->selectionOptions;
    }

    public function hasHints()
    {
        if ($this->hint) {
            return true;
        }
        if ($options = $this->getUsedOptions()) {
            foreach ($options as $option) {
                if ($option->hint) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getOptionsHints()
    {
        $result = [];
        if ($options = $this->getUsedOptions()) {
            foreach ($options as $option) {
                if ($option->hint) {
                    $result[] = $option->hint;
                }
            }
        }

        return $result;
    }

    public function getUsedOptions()
    {
        if ($this->usedOptions === null) {
            $this->usedOptions = [];

            $conditions = [
                [
                    'column' => 'parentStructureId',
                    'action' => '=',
                    'argument' => $this->id,
                ],
                [
                    'column' => 'type',
                    'action' => '=',
                    'argument' => 'structure',
                ],
            ];
            if ($records = persistableCollection::getInstance('structure_links')
                ->conditionalLoad('childStructureId', $conditions, ['position' => 'desc'])
            ) {
                $connectedOptionsIds = [];
                foreach ($records as &$record) {
                    $connectedOptionsIds[] = $record['childStructureId'];
                }
                $conditions = [
                    [
                        'column' => 'value',
                        'action' => 'IN',
                        'argument' => $connectedOptionsIds,
                    ],
                ];
                if ($records = persistableCollection::getInstance('module_product_parameter_value')
                    ->conditionalLoad('distinct(value)', $conditions, [], [], [], true)
                ) {
                    $usedOptionsIds = [];
                    foreach ($records as &$record) {
                        $usedOptionsIds[] = $record['value'];
                    }
                    $usedOptionsIdsOrdered = array_intersect($connectedOptionsIds, $usedOptionsIds);
                    $structureManager = $this->getService('structureManager');

                    foreach ($usedOptionsIdsOrdered as &$usedOptionId) {
                        if ($optionElement = $structureManager->getElementById($usedOptionId)) {
                            $this->usedOptions[] = $optionElement;
                        }
                    }
                }
            }
        }
        return $this->usedOptions;
    }

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $restrictLinkTypes = false)
    {
        return $this->getSelectionOptions();
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
        foreach ($records as &$record) {
            $record->delete();
        }
        parent::deleteElementData();
    }

    public function getConnectedFilterableCategories()
    {
        if ($this->connectedFilterableCategories === null) {
            $this->connectedFilterableCategories = [];
            if ($categoriesIds = $this->getConnectedFilterableCategoriesIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($categoriesIds as &$categoryId) {
                    if ($categoryId && $productElement = $structureManager->getElementById($categoryId)) {
                        $item = [];
                        $item['id'] = $productElement->id;
                        $item['title'] = $productElement->getTitle();
                        $item['select'] = true;
                        $this->connectedFilterableCategories[] = $item;
                    }
                }
            }
        }
        return $this->connectedFilterableCategories;
    }

    public function getConnectedFilterableCategoriesIds()
    {
        if ($this->connectedFilterableCategoriesIds === null) {
            $linksManager = $this->getService('linksManager');
            $this->connectedFilterableCategoriesIds = $linksManager->getConnectedIdList($this->id, 'productSelectionFilterableCategory', 'child');
        }
        return $this->connectedFilterableCategoriesIds;
    }
}