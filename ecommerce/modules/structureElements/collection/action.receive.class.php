<?php

class receiveCollection extends structureElementAction
{
    protected $loggable = true;


    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param collectionElement $structureElement
     * @return mixed|void
     */
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
            $connectedBrandListsIds = $structureElement->getConnectedCollectionsListsIds();
            foreach ($connectedBrandListsIds as &$connectedBrandListId) {
                $linksManager->unLinkElements($connectedBrandListId, $structureElement->id, 'collections');
            }

            if ($collectionsLists = $structureManager->getElementsByType('collectionsList')) {
                foreach ($collectionsLists as &$collectionList) {
                    if ($collectionList->connectAll || in_array($collectionList->id, $structureElement->collectionsListIds)) {
                        $linksManager->linkElements($collectionList->id, $structureElement->id, 'collections');
                    }
                }
            }

            // connect product with connected products
            $productsIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, 'collectionProduct');
            foreach ($structureElement->connectedProducts as $productId) {
                if (!isset($productsIdIndex[$productId])) {
                    $linksManager->linkElements($productId, $structureElement->id, 'collectionProduct', true);
                } else {
                    unset($productsIdIndex[$productId]);
                }
            }
            //delete obsolete connected products links
            foreach ($productsIdIndex as $productId => &$value) {
                $linksManager->unLinkElements($productId, $structureElement->id, 'collectionProduct');
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
            'collectionsListIds',
            'availabilityFilterEnabled',
            'parameterFilterEnabled',
            'discountFilterEnabled',
            'amountOnPageEnabled',
            'categoryFilterEnable',
            'connectedProducts',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


