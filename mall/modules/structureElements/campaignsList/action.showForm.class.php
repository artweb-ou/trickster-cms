<?php

class showFormCampaignsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->campaignsList = [];

            if ($campaignsElement = $structureManager->getElementByMarker('campaigns')) {
                $campaignsList = $structureManager->getElementsFlatTree($campaignsElement->id);

                $linksManager = $this->getService('linksManager');
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'campaignsList', 'parent');

                foreach ($campaignsList as &$campaign) {
                    $campaignItem = [];
                    $campaignItem['title'] = $campaign->title;
                    $campaignItem['structureName'] = $campaign->structureName;
                    $campaignItem['id'] = $campaign->id;
                    $campaignItem['select'] = isset($compiledLinks[$campaign->id]);

                    $structureElement->campaignsList[] = $campaignItem;
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = renderer::getInstance();
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}