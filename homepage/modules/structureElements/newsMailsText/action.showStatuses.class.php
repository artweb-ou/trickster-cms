<?php

class showStatusesNewsMailsText extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->historyList = [];
            if ($structureElement->hasActualStructureInfo()) {
                $addressesElement = $structureManager->getElementByMarker('newsMailsAddresses');
                $addresses = $addressesElement->getContentList();

                $emailDispatcher = $this->getService('EmailDispatcher');
                $historyIndex = $emailDispatcher->getReferencedDispatchmentHistory($structureElement->id);

                $sort = [];
                $historyList = [];
                foreach ($historyIndex as &$historyItem) {
                    $historyList[] = $historyItem;
                    $sort[] = strtolower($historyItem['email']);
                }
                array_multisort($sort, SORT_ASC, $historyList);
                $structureElement->historyList = $historyList;
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'newsMailsText.statuses.tpl');
            }
        }
    }
}


