<?php

class showFormProductSelection extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->categoriesList = [];
            $structureElement->levelsList = [];
            $connectedCategoriesIds = $structureElement->getConnectedCategoriesIds();
            $productCatalogues = $structureManager->getElementsByType('productCatalogue');
            foreach ($productCatalogues as $productCatalogue) {
                $categoryItem = [];
                $categoryItem['level'] = 0;
                $categoryItem['select'] = in_array($productCatalogue->id, $connectedCategoriesIds);
                $categoryItem['title'] = $productCatalogue->getTitle();
                $categoryItem['id'] = $productCatalogue->id;

                $structureElement->categoriesList[] = $categoryItem;
            }

            if ($categoriesFolder = $structureManager->getElementByMarker('categories')) {
                $categoriesList = $structureManager->getElementsFlatTree($categoriesFolder->id, 'container');

                foreach ($categoriesList as &$category) {
                    $categoryItem = [];
                    $categoryItem['level'] = $category->level - 3;
                    $categoryItem['select'] = in_array($category->id, $connectedCategoriesIds);
                    $categoryItem['title'] = $category->getTitle();
                    $categoryItem['id'] = $category->id;

                    $structureElement->categoriesList[] = $categoryItem;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}