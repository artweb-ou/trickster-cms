<?php

class showFullListNewsMailSubContentCategories extends structureElementAction
{
    /**
     * @param newsMailSubContentCategoriesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureManager->getElementsChildren($structureElement->id, 'container');

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
        }
    }
}

