<?php

class receiveSelectedProducts extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');

            //persist product data
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            $connectedProductsIds = $linksManager->getConnectedIdList($structureElement->id, "buttonConnectedMenu", "parent");
            if ($connectedProductsIds) {
                foreach ($connectedProductsIds as &$connectedProductId) {
                    $linksManager->unLinkElements($structureElement->id, $connectedProductId, "buttonConnectedMenu");
                }
            }
            $linksManager->linkElements($structureElement->id, $structureElement->buttonConnectedMenu, "buttonConnectedMenu");

            //todo: use ConnectedProductsProvider instead!
            // connect products
            $connectedProductsIds = $linksManager->getConnectedIdList($structureElement->id, "selectedProducts", "parent");
            if ($connectedProductsIds) {
                foreach ($connectedProductsIds as &$connectedProductId) {
                    if (!in_array($connectedProductId, $structureElement->products)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedProductId, "selectedProducts");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->products, $connectedProductsIds);
            foreach ($idsToConnect as $selectedProductId) {
                $linksManager->linkElements($structureElement->id, $selectedProductId, "selectedProducts");
            }

            $structureElement->updateConnectedCategories($structureElement->categoriesIds);

            // connect brands
            $connectedBrandsIds = $structureElement->getConnectedBrandsIds();
            if ($connectedBrandsIds) {
                foreach ($connectedBrandsIds as &$connectedBrandId) {
                    if (!in_array($connectedBrandId, $structureElement->brandsIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedBrandId, "selectedProductsBrand");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->brandsIds, $connectedBrandsIds);
            foreach ($idsToConnect as $selectedBrandId) {
                $linksManager->linkElements($structureElement->id, $selectedBrandId, "selectedProductsBrand");
            }

            // connect discounts
            $connectedDiscountsIds = $structureElement->getConnectedDiscountsIds();
            if ($connectedDiscountsIds) {
                foreach ($connectedDiscountsIds as &$connectedDiscountId) {
                    if (!in_array($connectedDiscountId, $structureElement->discountsIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedDiscountId, "selectedProductsDiscount");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->discountsIds, $connectedDiscountsIds);
            foreach ($idsToConnect as $selectedDiscountId) {
                $linksManager->linkElements($structureElement->id, $selectedDiscountId, "selectedProductsDiscount");
            }
            // connect product selection parameters
            $connectedProductSelectionsIds = $structureElement->getConnectedProductSelectionIds();
            if ($connectedProductSelectionsIds) {
                foreach ($connectedProductSelectionsIds as &$connectedProductSelectionsId) {
                    if (!in_array($connectedProductSelectionsId, $structureElement->productSelectionIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedProductSelectionsId, "selectedProductsProductSelection");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->productSelectionIds, $connectedProductSelectionsIds);
            foreach ($idsToConnect as $selectedProductSelectionId) {
                if ($selectedProductSelectionId !== 'none') {
                    $linksManager->linkElements($structureElement->id, $selectedProductSelectionId, "selectedProductsProductSelection");
                }
            }

            $structureElement->updateConnectedIcons($structureElement->iconIds);

            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'selectionType',
            'autoSelectionType',
            'content',
            'amount',
            'products',
            'displayMenus',
            'categoriesIds',
            'brandsIds',
            'discountsIds',
            'iconIds',
            'productSelectionIds',
            'priceSortingEnabled',
            'nameSortingEnabled',
            'dateSortingEnabled',
            'amountOnPageEnabled',
            'buttonTitle',
            'buttonUrl',
            'buttonConnectedMenu'
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

