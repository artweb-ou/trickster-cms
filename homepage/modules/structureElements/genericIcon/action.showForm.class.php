<?php

class showFormGenericIcon extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param genericIconElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->productIconId = $controller->getParameter('productId');
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        } else {
            $structureElement->setViewName('form');
        }

    }
}