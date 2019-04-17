<?php

class showOptionsPricingFormProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('form', $structureElement->getForm('optionsPricing'));
        $renderer->assign('contentSubTemplate', 'product.options_pricing_form.tpl');
    }
}
