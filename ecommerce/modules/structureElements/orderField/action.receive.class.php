<?php

class receiveOrderField extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if($fieldId = $structureElement->fieldId) {
                $element = $structureManager->getElementById($fieldId);
                if(!empty($element)) {
                    $structureElement->title = $element->getTitle();
                    $structureElement->fieldName = $element->fieldName;
                }
            }
            $structureElement->persistElementData();
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'value',
            'fieldId'
        ];
    }
}

