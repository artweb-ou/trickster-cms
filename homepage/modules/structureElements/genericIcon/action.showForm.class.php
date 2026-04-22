<?php

class showFormGenericIcon extends structureElementAction
{
    /**
     * @param genericIconElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->productIconId = $controller->getParameter('productId');
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        } else {
            $structureElement->setViewName('form');
        }

    }
}