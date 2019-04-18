<?php

class showFiltersSelectedProducts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $renderer = $this->getService('renderer');

        if ($structureElement->requested) {
            $productCataloguesInfo = [];
            if ($productCatalogues = $structureManager->getElementsByType('productCatalogue')) {
                $linksManager = $this->getService('linksManager');
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'selectedProductsCatalogue', 'parent');

                $productCataloguesInfo = [];
                foreach ($productCatalogues as &$productCatalogue) {
                    $productCatalogueInfo = [];
                    $productCatalogueInfo['linkExists'] = isset($compiledLinks[$productCatalogue->id]);
                    $productCatalogueInfo['title'] = $productCatalogue->getTitle();
                    $productCatalogueInfo['structureName'] = $productCatalogue->structureName;
                    $productCatalogueInfo['id'] = $productCatalogue->id;
                    $productCataloguesInfo[] = $productCatalogueInfo;
                }
            }
            $renderer->assign('productCataloguesInfo', $productCataloguesInfo);

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer->assign('contentSubTemplate', 'selectedProducts.filters.tpl');
            }
        }
    }
}

