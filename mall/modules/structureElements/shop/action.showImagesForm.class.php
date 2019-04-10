<?php

class showImagesFormShop extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            //PREPARE NEW IMAGE FORMS
            if ($structureElement->hasActualStructureInfo()) {
                $structureElement->newImageForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
            }
            $structureManager->getElementsChildren($structureElement->id);

            //LOAD IMAGES AND VARIATIONS
            $structureElement->images = [];
            if (count($structureElement->childrenList) > 0) {
                foreach ($structureElement->childrenList as &$childElement) {
                    if ($childElement->structureType == 'galleryImage') {
                        $structureElement->images[] = $childElement;
                    }
                }
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'shop.images.tpl');
        }
        $structureElement->setViewName('form');
    }
}

