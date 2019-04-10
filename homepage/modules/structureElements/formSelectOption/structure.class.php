<?php

class formSelectOptionElement extends structureElement
{
    public $dataResourceName = 'module_form_select_option';
    public $defaultActionName = 'show';
    public $role = 'content';
    private $hiddenFileds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hidden_fields'] = 'numbersArray';
    }

    public function getFieldsToBeHidden()
    {
        if ($this->hiddenFileds === null) {
            $this->hiddenFileds = [];
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');
            if ($connectedFieldsIds = $linksManager->getConnectedIdList($this->id, 'hiddenFields')) {
                foreach ($connectedFieldsIds as $fieldId) {
                    $field = $structureManager->getElementById($fieldId);

                    $this->hiddenFileds[] = [
                        'id' => $field->id,
                        'structureName' => $field->structureName,
                        'title' => $field->title,
                    ];
                }
            }
        }

        return $this->hiddenFileds;
    }
}