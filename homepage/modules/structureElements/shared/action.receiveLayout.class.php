<?php

/**
 * Class receiveLayoutShared
 *
 * @property ConfigurableLayoutsProviderInterface structureElement
 */
class receiveLayoutShared extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            $controller->redirect($structureElement->getUrl('showLayoutForm'));
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = $this->structureElement->getLayoutTypes();
    }

    public function setValidators(&$validators)
    {
    }
}