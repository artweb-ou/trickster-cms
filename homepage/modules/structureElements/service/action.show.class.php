<?php

class showService extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
//        $structureElement->setViewName('short');
        $structureElement->setViewName($structureElement->getCurrentLayout());
        if ($structureElement->final) {
            $structureElement->setViewName('details');

            if ($structureElement->final) {
                if ($feedbackElement = $structureManager->getElementById($structureElement->feedbackId)) {
                    if ($parents = $structureManager->getElementsParents($feedbackElement->id)) {
                        $firstParent = reset($parents);
                        $structureElement->feedbackURL = $firstParent->URL . 'service:' . $structureElement->id . '/';
                    }
                }
            }
        }
    }

}