<?php

class showProductsFormCategory extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($structureElement->catalogueElement = $structureManager->getElementByMarker('catalogue')) {
                $structureElement->catalogueElementURL = $structureElement->catalogueElement->URL . 'categoryId:' . $structureElement->id . '/';
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'category.form.products.tpl');
        }
    }
}