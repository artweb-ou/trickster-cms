<?php

class showProduction extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
        $structureElement->setViewName('short');
        if ($structureElement->requested) {
            $structureElement->setViewName('details');

            if ($structureElement->final) {
                $structureElement->galleriesList = [];
                if ($links = $linksManager->getElementsLinks($structureElement->id, 'connectedGallery', 'parent')) {
                    foreach ($links as &$link) {
                        if ($element = $structureManager->getElementById($link->childStructureId)) {
                            $structureElement->galleriesList[] = $element;
                        }
                    }
                }

                if ($feedbackElement = $structureManager->getElementById($structureElement->feedbackId)) {
                    if ($parents = $structureManager->getElementsParents($feedbackElement->id)) {
                        $firstParent = reset($parents);
                        $structureElement->feedbackURL = $firstParent->URL;
                    }
                }
            }
        }
    }
}