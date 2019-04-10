<?php

class receiveBasketInput extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param basketInputElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->fieldType = 'input';
            $structureElement->fieldName = 'field' . $structureElement->getId();
            if (!$structureElement->structureName) {
                $structureElement->structureName = $structureElement->fieldName;
            }

            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'required',
            'validator',
            'autocomplete',
            'helpLinkText',
            'helpLinkUrl',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}