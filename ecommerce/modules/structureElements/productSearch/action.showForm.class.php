<?php

class showFormProductSearch extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $renderer = $this->getService('renderer');
            $productCataloguesInfo = [];
            if ($productCatalogues = $structureManager->getElementsByType('productCatalogue')) {
                $linksManager = $this->getService('linksManager');
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'productSearchCatalogue', 'parent');

                $structureElement->productCataloguesInfo = [];
                foreach ($productCatalogues as &$productCatalogue) {
                    $productCatalogueInfo = [];
                    $productCatalogueInfo['select'] = isset($compiledLinks[$productCatalogue->id]);
                    $productCatalogueInfo['title'] = $productCatalogue->getTitle();
                    $productCatalogueInfo['id'] = $productCatalogue->id;
                    $structureElement->productCataloguesInfo[] = $productCatalogueInfo;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}