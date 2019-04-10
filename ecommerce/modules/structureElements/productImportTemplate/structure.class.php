<?php

class productImportTemplateElement extends structureElement
{
    protected $allowedTypes = ['productImportTemplateColumn'];
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_productimporttemplate';
    public $defaultActionName = 'showForm';
    public $role = 'container';
    protected $templateData;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['importOrigin'] = 'text';
        $moduleStructure['priceAdjustment'] = 'text';
        $moduleStructure['delimiter'] = 'text';
        $moduleStructure['ignoreFirstRow'] = 'text';
    }

    public function getTemplateData()
    {
        if (is_null($this->templateData)) {
            $this->templateData = [
                'importOrigin' => $this->importOrigin,
                'priceAdjustment' => $this->priceAdjustment,
                'delimiter' => $this->delimiter,
                'ignoreFirstRow' => $this->ignoreFirstRow,
                'columns' => [],
            ];
            if ($columns = $this->getChildrenList()) {
                foreach ($columns as &$column) {
                    $data["productVariable"] = $column->productVariable;
                    $data["columnName"] = $column->title;
                    $data["columnNumber"] = $column->columnNumber;
                    $data["mandatory"] = $column->mandatory;
                    if ($column->productVariable == "parameter") {
                        $data["elementId"] = $column->productParameterId;
                    }
                    $this->templateData['columns'][] = $data;
                }
            }
        }
        return $this->templateData;
    }

    public function getTemplateColumnsList()
    {
        if ($children = $this->getChildrenList()) {
            $sort = [];
            foreach ($children as &$child) {
                $sort[] = intval($child->columnNumber);
            }
            array_multisort($sort, SORT_ASC, $children);
        }
        return $children;
    }
}


