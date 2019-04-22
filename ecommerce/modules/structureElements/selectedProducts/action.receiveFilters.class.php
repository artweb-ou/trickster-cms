<?php

class receiveFiltersSelectedProducts extends structureElementAction
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

            // filters related
            $linksManager = $this->getService('linksManager');
            $connectedParametersIds = $structureElement->getConnectedParametersIds();
            if ($connectedParametersIds) {
                foreach ($connectedParametersIds as &$connectedParameterId) {
                    if (!in_array($connectedParameterId, $structureElement->parametersIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedParameterId, "selectedProductsParameter");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->parametersIds, $connectedParametersIds);
            foreach ($idsToConnect as $idToConnect) {
                $linksManager->linkElements($structureElement->id, $idToConnect, "selectedProductsParameter");
            }

            $connnectedCataloguesIds = $linksManager->getConnectedIdList($structureElement->id, 'selectedProductsCatalogue');

            foreach ($connnectedCataloguesIds as $connnectedCatalogueId) {
                $linksManager->unLinkElements($structureElement->id, $connnectedCatalogueId, "selectedProductsCatalogue");
            }
            if ($structureElement->catalogueFilterId) {
                $linksManager->linkElements($structureElement->id, $structureElement->catalogueFilterId, "selectedProductsCatalogue");
            }

            $controller->redirect($structureElement->getUrl('showFilters'));
        } else {
            $structureElement->executeAction("showFilters");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'filterCategory',
            'filterBrand',
            'filterPriceEnabled',
            'filterDiscount',
            'availabilityFilterEnabled',
            'priceInterval',
            'parametersIds',
            'catalogueFilterId',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

