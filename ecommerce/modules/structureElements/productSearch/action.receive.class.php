<?php

class receiveProductSearch extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productSearchElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            // save info
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

            foreach ($connnectedCataloguesIds as $connectedCatalogueId) {
                if ($structureElement->catalogueFilterId != $connectedCatalogueId) {
                    $linksManager->unLinkElements($structureElement->id, $connectedCatalogueId, "productSearchCatalogue");
                }
            }
            if ($structureElement->catalogueFilterId) {
                $linksManager->linkElements($structureElement->id, $structureElement->catalogueFilterId, "productSearchCatalogue");
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

