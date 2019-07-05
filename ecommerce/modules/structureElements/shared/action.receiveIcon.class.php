<?php

class receiveIconShared extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {

        if ($this->validated) {
            /**
             * @var $linksManager linksManager
             */
            $linksManager = $this->getService('linksManager');
            $conectedIconsList = $linksManager->getConnectedIdList($parentId, 'genericIconProduct');
        }
        $structureElement->executeAction('showIconForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'connectedIconIds'
        ];
    }
}