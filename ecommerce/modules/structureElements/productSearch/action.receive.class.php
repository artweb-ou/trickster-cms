<?php

class receiveProductSearch extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            // save info
            $structureElement->prepareActualData();
            if ($structureElement->priceInterval !== null) {
                $structureElement->priceInterval = (int)$structureElement->priceInterval;
            }
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            // save relations
            $linksManager = $this->getService('linksManager');
            $connectedParametersIds = $structureElement->getConnectedParametersIds();
            if ($connectedParametersIds) {
                foreach ($connectedParametersIds as &$connectedParameterId) {
                    if (!in_array($connectedParameterId, $structureElement->parametersIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedParameterId, "productSearchParameter");
                    }
                }
            }
            $idsToConnect = array_diff($structureElement->parametersIds, $connectedParametersIds);
            foreach ($idsToConnect as $idToConnect) {
                $linksManager->linkElements($structureElement->id, $idToConnect, "productSearchParameter");
            }

            $connnectedCataloguesIds = $linksManager->getConnectedIdList($structureElement->id, 'productSearchCatalogue');

            if (!in_array($structureElement->catalogueFilterId, $connnectedCataloguesIds)) {
                foreach ($connnectedCataloguesIds as $connnectedCatalogueId) {
                    $linksManager->unLinkElements($structureElement->id, $connnectedCatalogueId, "productSearchCatalogue");
                }
                if ($structureElement->catalogueFilterId) {
                    $linksManager->linkElements($structureElement->id, $structureElement->catalogueFilterId, "productSearchCatalogue");
                }
            }

            $controller->redirect($structureElement->URL);
            $structureElement->setViewName('result');
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'filterCategory',
            'filterBrand',
            'filterDiscount',
            'availabilityFilterEnabled',
            'filterPrice',
            'priceInterval',
            'displayMenus',
            'parametersIds',
            'catalogueFilterId',
            'sortingEnabled',
            'pageDependent',
            'checkboxesForParameters',
            'pricePresets',
        ];
    }
}

