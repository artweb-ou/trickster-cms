<?php

class showLinkList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($fixedElement = $structureElement->getFixedElement()) {
            if ($structureElement->title == '') {
                $structureElement->title = $fixedElement->title;
            }

            $structureElement->URL = $fixedElement->URL;
        }
        $structureElement->setViewName($structureElement->getCurrentLayout());
        $structureElement->linkItems = $structureManager->getElementsChildren($structureElement->id);

        //        $structureElement->setViewName('short');
        $structureElement->setViewName($structureElement->getCurrentLayout());
//        if ($structureElement->final) {
//            $structureElement->setViewName('details');
//
//            if ($structureElement->final) {
//                if ($feedbackElement = $structureManager->getElementById($structureElement->feedbackId)) {
//                    if ($parents = $structureManager->getElementsParents($feedbackElement->id)) {
//                        $firstParent = reset($parents);
//                        $structureElement->feedbackURL = $firstParent->URL . 'service:' . $structureElement->id . '/';
//                    }
//                }
//            }
//        }
    }
}