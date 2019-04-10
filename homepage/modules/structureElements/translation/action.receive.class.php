<?php

class receiveTranslation extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $valueFields = ['valueText', 'valueTextarea', 'valueHtml'];
            $selectedValueField = 'value' . ucfirst($structureElement->valueType);
            foreach ($valueFields as &$fieldName) {
                if ($fieldName != $selectedValueField) {
                    $structureElement->$fieldName = '';
                }
            }
            $structureElement->persistElementData();
            $translationsManager = $this->getService('translationsManager');
            $translationsManager->generateTranslationsFile('public_translations');

            if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->redirect($parentElement->URL);
            }
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'structureName',
            'valueType',
            'valueText',
            'valueTextarea',
            'valueHtml',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['structureName'][] = 'notEmpty';
    }
}

