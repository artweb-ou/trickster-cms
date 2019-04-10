<?php

class showFullListNewsMailSubContentCategories extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureManager->getElementsChildren($structureElement->id, 'container');

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
        }
    }
}

