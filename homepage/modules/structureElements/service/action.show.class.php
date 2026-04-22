<?php

class showService extends structureElementAction
{
    /**
     * @param serviceElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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