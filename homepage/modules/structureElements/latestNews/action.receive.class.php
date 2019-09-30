<?php

class receiveLatestNews extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');

            //persist latestNews data
            $structureElement->prepareActualData();

         //   if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
         //   }

            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();

            $buttonConnectedIds = $linksManager->getConnectedIdList($structureElement->id, "buttonConnectedMenu", "parent");
            if ($buttonConnectedIds) {
                foreach ($buttonConnectedIds as &$buttonConnectedId) {
                    $linksManager->unLinkElements($structureElement->id, $buttonConnectedId, "buttonConnectedMenu");
                }
            }
            $linksManager->linkElements($structureElement->id, $structureElement->buttonConnectedMenu, "buttonConnectedMenu");
            //persist connected news
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'selectedNews');
            foreach ($structureElement->newsManualSearch as &$newsId) {
                if (!isset($compiledLinks[$newsId])) {
                    $linksManager->linkElements($structureElement->id, $newsId, 'selectedNews');
                } else {
                    unset($compiledLinks[$newsId]);
                }
            }

            foreach ($compiledLinks as &$link) {
                $link->delete();
            }

            // connect newslists
            if ($connectedNewsListsIds = $linksManager->getConnectedIdList($structureElement->id, 'latestNewsNewsList', 'parent')
            ) {
                foreach ($connectedNewsListsIds as &$connectedNewsListsId) {
                    if (!in_array($connectedNewsListsId, $structureElement->formNewsListsLimitIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedNewsListsId, "latestNewsNewsList");
                    }
                }
            }
            foreach ($structureElement->formNewsListsLimitIds as $formNewsListsLimitId) {
                $linksManager->linkElements($structureElement->id, $formNewsListsLimitId, "latestNewsNewsList");
            }
            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'newsDisplayType',
            'newsDisplayAmount',
            'newsManualSearch',
            'displayMenus',
            'formNewsListsLimitIds',
            'itemsOnPage',
            'orderType',
            'buttonTitle',
            'buttonUrl',
            'buttonConnectedMenu',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}