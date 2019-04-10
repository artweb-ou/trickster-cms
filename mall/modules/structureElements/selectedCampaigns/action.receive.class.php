<?php

class receiveSelectedCampaigns extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            // connect campaigns
            $campaignsElement = $structureManager->getElementByMarker('campaigns');
            $campaignsList = $structureManager->getElementsFlatTree($campaignsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'selectedCampaignsCampaign', 'parent');

            foreach ($campaignsList as &$campaign) {
                if (isset($compiledLinks[$campaign->id]) && !in_array($campaign->id, $structureElement->receivedCampaignsIds) && !$structureElement->connectAll
                ) {
                    $linksManager->unLinkElements($structureElement->id, $campaign->id, 'selectedCampaignsCampaign');
                } elseif (!isset($compiledLinks[$campaign->id]) && ($structureElement->connectAll || in_array($campaign->id, $structureElement->receivedCampaignsIds))
                ) {
                    $linksManager->linkElements($structureElement->id, $campaign->id, 'selectedCampaignsCampaign');
                }
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'displayMenus',
            'receivedCampaignsIds',
            'connectAll',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


