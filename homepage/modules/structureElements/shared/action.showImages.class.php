<?php

class showImagesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($structureElement->hasActualStructureInfo()) {
                $structureElement->newImageForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
            }
            $structureManager->getElementsChildren($structureElement->id);

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'shared.images.tpl');
        }
    }
}