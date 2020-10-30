<?php

class receivePaymentSettingsPayanywayPaymentMethod extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->importExternalData(array());
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = array(
            'projectId',
            'testMode',
            'controlCode',
            'bankURL',
        );
    }

    public function setValidators(&$validators)
    {
    }
}

