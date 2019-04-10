<?php

class parameterProductFilter extends productFilter
{
    protected $type = 'parameter';
    protected $selectionId;

    public function setSelectionId($id)
    {
        $this->selectionId = $id;
    }

    public function getSelectionId()
    {
        return $this->selectionId;
    }

    public function getTitle()
    {
        $title = '';
        if ($selectionElement = $this->getSelectionElement()) {
            $title = $selectionElement->title;
        }
        return $title;
    }

    public function getSelectionElement()
    {
        return $this->getService('structureManager')->getElementById($this->selectionId);
    }

    public function canBeDisplayedInCategory()
    {
        $selectionElement = $this->getSelectionElement();
        return $selectionElement;
    }

    public function getOptionsInfo()
    {
        $info = [];
        if ($this->options) {
            $selectionValuesIds = $this->getService('linksManager')
                ->getConnectedIdList($this->selectionId, 'structure', 'parent');
            $structureManager = $this->getService('structureManager');
            $argumentMap = array_flip($this->arguments);
            $optionsIds = array_intersect($selectionValuesIds, $this->options);

            foreach ($optionsIds as &$optionId) {
                $valueElement = $structureManager->getElementById($optionId);
                $info[] = [
                    'title' => $valueElement->title,
                    'selected' => isset($argumentMap[$valueElement->id]),
                    'id' => $valueElement->id,
                ];
            }
        }
        return $info;
    }

    protected function limitOptions(array $productsIds)
    {
        if ($productsIds) {
            $collection = persistableCollection::getInstance('module_product_parameter_value');
            $conditions = [
                [
                    'productId',
                    'IN',
                    $productsIds,
                ],
                [
                    'parameterId',
                    '=',
                    $this->selectionId,
                ],
            ];
            if ($this->options) {
                $conditions[] = [
                    'value',
                    'IN',
                    $this->options,
                ];
                $this->options = [];
            }
            $records = $collection->conditionalLoad(['distinct(value)'], $conditions, [], [], [], true);
            if ($records) {
                foreach ($records as &$record) {
                    $this->options[] = $record['value'];
                }
            }
        }
    }

    protected function loadRelatedIds()
    {
        $this->relatedIds = [];
        if ($this->arguments) {
            $collection = persistableCollection::getInstance('module_product_parameter_value');
            $conditions = [
                [
                    'value',
                    'IN',
                    $this->arguments,
                ],
                [
                    'parameterId',
                    '=',
                    $this->selectionId,
                ],
            ];
            if ($records = $collection->conditionalLoad('productId', $conditions)) {
                foreach ($records as &$record) {
                    $this->relatedIds[] = $record['productId'];
                }
            }
        }
    }
}