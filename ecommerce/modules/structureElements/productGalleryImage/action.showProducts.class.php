<?php

class showProductsProductGalleryImage extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->contentList = $structureManager->getElementsChildren($structureElement->id);
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = renderer::getInstance();
                $renderer->assign('contentSubTemplate', 'shared.contentlist_singlepage.tpl');
            }
        }
    }
}


