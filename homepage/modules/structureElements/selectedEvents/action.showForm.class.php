<?php

class showFormSelectedEvents extends structureElementAction
{
    /**
     * @param selectedEventsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->fixedId) {
            $structureElement->connectedMenu = $structureManager->getElementById($structureElement->fixedId);
        }
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $parentElement = $structureManager->getElementsFirstParent($structureElement->id);

            $renderer->assign('parentLayout', $parentElement ? $parentElement->layout : '');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}
