<?php

class formSelectElement extends formFieldStructureElement
{
    public $dataResourceName = 'module_form_field';
    protected $allowedTypes = ['formSelectOption'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $optionsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['fieldName'] = 'text';
        $moduleStructure['fieldType'] = 'text';
        $moduleStructure['dataChunk'] = 'text';
        $moduleStructure['required'] = 'checkbox';
        $moduleStructure['validator'] = 'text';
        $moduleStructure['autocomplete'] = 'text';
        $moduleStructure['selectionType'] = 'text';
    }

    public function getSelectionType()
    {
        if (!$this->selectionType) {
            return 'dropdown';
        } else {
            return $this->selectionType;
        }
    }

    public function getOptionsList()
    {
        if (is_null($this->optionsList)) {
            $structureManager = $this->getService('structureManager');
            if ($this->autocomplete == 'service') {
                $languagesManager = $this->getService('languagesManager');
                $this->optionsList = [];
                if ($servicesList = $structureManager->getElementsByType('service', $languagesManager->getCurrentLanguageId())
                ) {
                    $sort = [];
                    foreach ($servicesList as &$service) {
                        $option = new stdClass();
                        $option->title = $service->title;
                        $sort[] = $service->title;

                        $this->optionsList[] = $option;
                    }
                    array_multisort($sort, SORT_ASC, $this->optionsList);
                }
            } else {
                $this->optionsList = $structureManager->getElementsChildren($this->id);
            }
        }
        return $this->optionsList;
    }

    public function getDataChunkType()
    {
        if ($this->selectionType == 'checkbox') {
            return 'array';
        } else {
            return 'text';
        }
    }
}

