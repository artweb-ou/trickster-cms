<?php

class showCampaignsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $renderer = renderer::getInstance();
            $renderer->assign('campaigns', $structureElement->getCampaigns());
        }
        $structureElement->setTemplate('campaignsList.content.tpl');
    }
}

