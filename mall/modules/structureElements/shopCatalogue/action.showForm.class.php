<?php

class showFormShopCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->categoriesList = [];

        $categoriesElement = $structureManager->getElementByMarker('shopCategories');
        if ($categoriesElement) {
            $categoriesList = $structureManager->getElementsFlatTree($categoriesElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                $structureElement::LINK_TYPE_CATEGORY, 'parent');

            foreach ($categoriesList as &$category) {
                $categoryItem = [];
                $categoryItem['categoryLevel'] = $category->level - 3;
                $categoryItem['title'] = $category->title;
                $categoryItem['structureName'] = $category->structureName;
                $categoryItem['id'] = $category->id;
                $categoryItem['select'] = isset($compiledLinks[$category->id]);

                $structureElement->categoriesList[] = $categoryItem;
            }
        }
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}