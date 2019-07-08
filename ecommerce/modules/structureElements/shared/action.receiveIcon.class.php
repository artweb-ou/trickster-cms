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
            $linkType = 'genericIcon'.ucfirst($structureElement->structureType);
            $linksManager = $this->getService('linksManager');
            $icons = $structureElement->connectedIconIds;
            $connectedGenericIconsList = $linksManager->getConnectedIdIndex($structureElement->id, $linkType);
            foreach ($structureElement->connectedIconIds as $connectedIcon) {
                if (!isset($connectedGenericIconsList[(int)$connectedIcon])) {
                    $linksManager->linkElements($connectedIcon, $structureElement->id, $linkType);
                } else {
                    unset ($connectedGenericIconsList[$connectedIcon]);
                }
            }
            if(!empty($connectedGenericIconsList)) {
                foreach ($connectedGenericIconsList as $genericIcon => $value) {
                    $linksManager->unLinkElements($structureElement->id, $genericIcon, $linkType);
                }
            }
            $url = $structureElement->getUrl();
        }
        $controller->redirect($url.'id:'.$structureElement->id.'/action:showIconForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'connectedIconIds'
        ];
    }
}