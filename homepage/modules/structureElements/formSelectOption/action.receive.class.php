<?php

class receiveFormSelectOption extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;

            $structureElement->persistElementData();

            //connect fields to be hidden
            $linksManager = $this->getService('linksManager');
            $hiddenFildsIds = $linksManager->getConnectedIdIndex($structureElement->id, 'hiddenFields', 'child');
            foreach ($structureElement->hidden_fields as $fieldId) {
                if (!isset($hiddenFildsIds[$fieldId])) {
                    $linksManager->linkElements($fieldId, $structureElement->id, 'hiddenFields');
                } else {
                    unset($hiddenFildsIds[$fieldId]);
                }
            }
            foreach ($hiddenFildsIds as $fieldId => &$value) {
                $linksManager->unLinkElements($fieldId, $structureElement->id, 'hiddenFields');
            }

            if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->redirect($parentElement->URL);
            }
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hidden_fields',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}


