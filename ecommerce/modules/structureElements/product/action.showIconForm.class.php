<?php

class showIconFormProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->newIconForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
        }

        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'component.show.icon_form.tpl');
    }
}