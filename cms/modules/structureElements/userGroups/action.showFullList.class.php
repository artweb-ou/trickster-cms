<?php

class showFullListUserGroups extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureManager->getElementsChildren($structureElement->id, 'container');

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'shared.contentlist_singlepage.tpl');
        }
    }
}