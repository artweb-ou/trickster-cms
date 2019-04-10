<?php

class receiveCampaign extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->startDate && $structureElement->startDate == $structureElement->endDate) {
            $structureElement->setFormError('endDate');
            $this->validated = false;
        }
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $linksManager = $this->getService('linksManager');
            $connectedShopId = $structureElement->getConnectedShopId();
            if ($connectedShopId && $connectedShopId != $structureElement->shopId) {
                $linksManager->unLinkElements($connectedShopId, $structureElement->id, campaignElement::LINK_TYPE_SHOP);
                $linksManager->unLinkElements($structureElement->id, $connectedShopId, campaignElement::LINK_TYPE_SHOP);
            }
            if ($structureElement->shopId) {
                $linksManager->linkElements($structureElement->shopId, $structureElement->id, campaignElement::LINK_TYPE_SHOP, true);
            }
            foreach ($structureManager->getElementsByType('campaignsList') as $campaignsList) {
                if ($campaignsList->connectAll) {
                    $linksManager->linkElements($campaignsList->id, $structureElement->id, 'campaignsList');
                }
            }
            foreach ($structureManager->getElementsByType('selectedCampaigns') as $selectedCampaigns) {
                if ($selectedCampaigns->connectAll) {
                    $linksManager->linkElements($selectedCampaigns->id, $structureElement->id, 'selectedCampaignsCampaign');
                }
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'date',
            'title',
            'introduction',
            'content',
            'image',
            'shopId',
            'startDate',
            'endDate',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

