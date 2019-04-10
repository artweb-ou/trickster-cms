<?php

class showFullListNewsMailsTexts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $contentList = array_reverse($structureElement->getChildrenList() ?: []);
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
        }
    }
}


