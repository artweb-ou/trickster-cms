<?php

class receiveCampaignsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();

            // link campaigns
            $campaignsElement = $structureManager->getElementByMarker('campaigns');
            $campaignsList = $structureManager->getElementsFlatTree($campaignsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'campaignsList', 'parent');

            foreach ($campaignsList as &$campaign) {
                if (isset($compiledLinks[$campaign->id]) && !in_array($campaign->id, $structureElement->campaigns) && !$structureElement->connectAll
                ) {
                    $linksManager->unLinkElements($structureElement->id, $campaign->id, 'campaignsList');
                } elseif (!isset($compiledLinks[$campaign->id]) && ($structureElement->connectAll || in_array($campaign->id, $structureElement->campaigns))
                ) {
                    $linksManager->linkElements($structureElement->id, $campaign->id, 'campaignsList');
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
            'campaigns',
            'connectAll',
            'content',
            'columns',
            'hidden',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


