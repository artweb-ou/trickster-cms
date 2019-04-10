<?php

class showTextsFormShop extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'shop.texts.tpl');
        }
        $structureElement->setViewName('form');
    }
}

