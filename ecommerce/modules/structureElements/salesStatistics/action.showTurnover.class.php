<?php

class showTurnoverSalesStatistics extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureManager->getElementsChildren($structureElement->id, 'container');
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('chartData', $structureElement->getChartData());
            $renderer->assign('contentSubTemplate', 'salesStatistics.turnover.tpl');

            $pageNumber = max(1, (int)$controller->getParameter('page'));
            $pagerURL = $structureElement->getUrlWithFilter();
            $pager = new pager($pagerURL, $structureElement->getTotalListElements(), 20, $pageNumber, 'page');
            $renderer->assign('listElements', $structureElement->getListElements($pageNumber, 20));
            $renderer->assign('pager', $pager);
            $renderer->assign('listType', $structureElement->getFilterParameter('list'));
            $currencySelector = $this->getService('CurrencySelector');
            $currentCurrencyItem = $currencySelector->getDefaultCurrencyItem();
            $renderer->assign('symbol', $currentCurrencyItem->symbol);
            $renderer->assign('productsTotal', $structureElement->getProductsTotal());
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'start',
            'end',
            'group',
            'category',
            'product',
            'user_group',
            'display',
            'list',
        ];
    }
}

