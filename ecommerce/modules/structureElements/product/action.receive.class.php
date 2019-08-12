<?php

class receiveProduct extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');

            $structureElement->quantity = (int)$structureElement->quantity;
            //persist product data
            $structureElement->prepareActualData();

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            if ($structureElement->code == '') {
                $structureElement->code = $structureElement->id;
            }
            $structureElement->persistElementData();

            //check if product is only linked with products catalogue by structure link
            //this is required when product has been added from within category, possible to-do for refactoring
            if ($catalogueElement = $structureManager->getElementByMarker('catalogue')) {
                $linksManager->linkElements($catalogueElement->id, $structureElement->id, 'structure');

                //delete all structure links not leading to catalogue element
                if ($parentIdList = $linksManager->getConnectedIdList($structureElement->id, 'structure', 'child')) {
                    foreach ($parentIdList as &$id) {
                        if ($catalogueElement->id != $id) {
                            $linksManager->unLinkElements($id, $structureElement->id, 'structure');
                        }
                    }
                }
            }

            if ($publicCatalogues = $structureManager->getElementsByType('productCatalogue')) {
                foreach ($publicCatalogues as &$catalogue) {
                    if (!$catalogue->categorized) {
                        $linksManager->linkElements($catalogue->id, $structureElement->id, 'productCatalogueProduct');
                    }
                }
            }

            // link product with selected discounts
            $discountsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'discountProduct', 'child');
            foreach ($structureElement->discounts as $discountId) {
                if (!isset($discountsIdIndex[$discountId])) {
                    $linksManager->linkElements($discountId, $structureElement->id, 'discountProduct');
                } else {
                    unset($discountsIdIndex[$discountId]);
                }
            }
            //delete obsolete discount links
            foreach ($discountsIdIndex as $discountId => &$value) {
                $linksManager->unLinkElements($discountId, $structureElement->id, 'discountProduct');
            }

            // connect product with brand
            if ($structureElement->brandId) {
                $linksManager->linkElements($structureElement->brandId, $structureElement->id, 'productbrand');
            }
            //delete obsolete brand links
            $brandIds = $linksManager->getConnectedIdList($structureElement->id, 'productbrand', 'child');
            foreach ($brandIds as &$brandId) {
                if ($brandId != $structureElement->brandId) {
                    $linksManager->unLinkElements($brandId, $structureElement->id, 'productbrand');
                }
            }

            // connect product with categories
            $categoriesIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'catalogue', 'child');
            foreach ($structureElement->categories as $categoryId) {
                if (!isset($categoriesIdIndex[$categoryId])) {
                    $linksManager->linkElements($categoryId, $structureElement->id, 'catalogue');
                } else {
                    unset($categoriesIdIndex[$categoryId]);
                }
            }
            //delete obsolete categories links
            foreach ($categoriesIdIndex as $categoryId => &$value) {
                $linksManager->unLinkElements($categoryId, $structureElement->id, 'catalogue');
            }

            // connect product with connected products
            $productsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'connected');
            foreach ($structureElement->products as $productId) {
                if (!isset($productsIdIndex[$productId])) {
                    $linksManager->linkElements($productId, $structureElement->id, 'connected', true);
                } else {
                    unset($productsIdIndex[$productId]);
                }
            }
            //delete obsolete connected products links
            foreach ($productsIdIndex as $productId => &$value) {
                $linksManager->unLinkElements($productId, $structureElement->id, 'connected');
            }

            // connect product with connected products
            $productsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'connected2');
            foreach ($structureElement->products2 as $productId) {
                if (!isset($productsIdIndex[$productId])) {
                    $linksManager->linkElements($productId, $structureElement->id, 'connected2', true);
                } else {
                    unset($productsIdIndex[$productId]);
                }
            }
            //delete obsolete connected products links
            foreach ($productsIdIndex as $productId => &$value) {
                $linksManager->unLinkElements($productId, $structureElement->id, 'connected2');
            }

            // connect product with connected products
            $productsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'connected3');
            foreach ($structureElement->products3 as $productId) {
                if (!isset($productsIdIndex[$productId])) {
                    $linksManager->linkElements($productId, $structureElement->id, 'connected3', true);
                } else {
                    unset($productsIdIndex[$productId]);
                }
            }
            //delete obsolete connected products links
            foreach ($productsIdIndex as $productId => &$value) {
                $linksManager->unLinkElements($productId, $structureElement->id, 'connected3');
            }

            //connect product with connected category
            $connectedProductCategoriesIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'connectedCategory');

            foreach ($structureElement->connectedProductCategories as $categoryId) {
                if (!isset($connectedProductCategoriesIdIndex[$categoryId])) {
                    $linksManager->linkElements($categoryId, $structureElement->id, 'connectedCategory', true);
                } else {
                    unset($connectedProductCategoriesIdIndex[$categoryId]);
                }
            }
            //delete obsolete connected categories links
            foreach ($connectedProductCategoriesIdIndex as $categoryId => $value) {
                $linksManager->unLinkElements($categoryId, $structureElement->id, 'connectedCategory');
            }

            $collectionsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'collection', 'child');
            foreach ($structureElement->collectionsListId as $collectionsListId) {
                if (!isset($categoriesIdIndex[$collectionsListId])) {
                    $linksManager->linkElements($collectionsListId, $structureElement->id, 'collection');
                } else {
                    unset($categoriesIdIndex[$collectionsListId]);
                }
            }
            //delete obsolete categories links
            foreach ($collectionsIdIndex as $collectionId => &$value) {
                $linksManager->unLinkElements($collectionId, $structureElement->id, 'collection');
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'subTitle',
            'inactive',
            'showincategory',
            'categories',
            'products',
            'products2',
            'products3',
            'price',
            'oldPrice',
            'color',
            'code',
            'brandId',
            'discounts',
            'availability',
            'quantity',
            'minimumOrder',
            'connectedProductCategories',
            'qtFromConnectedCategories',
            'unit',
            'collectionsListId'
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

