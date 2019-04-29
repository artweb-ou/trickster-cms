<?php

class showImagesProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($structureElement->hasActualStructureInfo()) {
                $structureElement->newForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('linkType', 'productImage');
            $renderer->assign('form', $structureElement->getForm('images'));
        }
    }
}