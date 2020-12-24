<?php

class showCampaign extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->parentMenuElement = $structureManager->getElementsFirstParent($structureElement->id);
        if ($structureElement->requested) {
            $linksManager = $this->getService('linksManager');
            $list = $linksManager->getConnectedIdList($structureElement->id, 'campaigns', 'child');
            foreach ($list as $id) {
                if ($shop = $structureManager->getElementById($id)) {
                    $structureElement->shopURL = $shop->URL;
                    break;
                }
            }
            $structureElement->setViewName('details');
        } else {
            $structureElement->setViewName('short');
        }
    }
}

