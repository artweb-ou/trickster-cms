<?php

class showFormSelectedEvents extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->fixedId) {
            $structureElement->connectedMenu = $structureManager->getElementById($structureElement->fixedId);
        }
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $parentElement = $structureManager->getElementsFirstParent($structureElement->id);

            $renderer->assign('parentLayout', $parentElement ? $parentElement->layout : '');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}
