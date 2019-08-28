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
            'categoryFilterEnable'
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


