<?php

class receiveBrand extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();
            $linksManager = $this->getService('linksManager');
            $connectedBrandListsIds = $structureElement->getConnectedBrandsListsIds();
            foreach ($connectedBrandListsIds as &$connectedBrandListId) {
                $linksManager->unLinkElements($connectedBrandListId, $structureElement->id, 'brands');
            }
            // connect all brandslists configured to show all brands
            if ($brandsLists = $structureManager->getElementsByType('brandsList')) {
                foreach ($brandsLists as &$brandsList) {
                    if ($brandsList->connectAll || in_array($brandsList->id, $structureElement->brandsListsIds)) {
                        $linksManager->linkElements($brandsList->id, $structureElement->id, 'brands');
                    }
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'content',
            'image',
            'link',
            'priceSortingEnabled',
            'nameSortingEnabled',
            'dateSortingEnabled',
            'introduction',
            'structureName',
            'brandsListsIds',
            'availabilityFilterEnabled',
            'parameterFilterEnabled',
            'discountFilterEnabled',
            'amountOnPageEnabled',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


