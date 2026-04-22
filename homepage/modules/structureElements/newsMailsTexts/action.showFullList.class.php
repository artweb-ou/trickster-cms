<?php

class showFullListNewsMailsTexts extends structureElementAction
{
    /**
     * @param newsMailsTextsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $contentList = array_reverse($structureElement->getChildrenList() ?: []);
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
        }
    }
}


