<?php

class showFullListCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $filterCategories = $filterBrands = $filterDiscounts = [];
            if ($ids = $controller->getParameter('category')) {
                $filterCategories = $this->getElementsByIdList($structureManager, $ids);
            }
            if ($ids = $controller->getParameter('brand')) {
                $filterBrands = $this->getElementsByIdList($structureManager, $ids);
            }
            if ($ids = $controller->getParameter('discount')) {
                $filterDiscounts = $this->getElementsByIdList($structureManager, $ids);
            }
            $renderer = $this->getService('renderer');
            $renderer->assign('productsList', $structureElement->getProductsPage());
            $renderer->assign('pager', $structureElement->pager);
            $renderer->assign('filterCategories', $filterCategories);
            $renderer->assign('filterBrands', $filterBrands);
            $renderer->assign('filterDiscounts', $filterDiscounts);
            $renderer->assign('contentSubTemplate', 'catalogue.list.tpl');
            $structureElement->setTemplate('shared.content.tpl');
        }
    }

    public function getElementsByIdList($structureManager, $ids)
    {
        $result = [];
        foreach ($ids as $id) {
            if ($element = $structureManager->getElementById($id)) {
                $result[] = $element;
            }
        }
        return $result;
    }
}