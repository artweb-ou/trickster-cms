<?php

class showFullListIcons extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
        }
    }
}

