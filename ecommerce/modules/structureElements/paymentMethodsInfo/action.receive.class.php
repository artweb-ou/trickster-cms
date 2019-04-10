<?php

class receivePaymentMethodsInfo extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'structureName',
            'title',
            'link',
            'displayMenus',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}
