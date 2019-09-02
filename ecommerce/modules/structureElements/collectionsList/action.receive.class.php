<?php

class receiveCollectionsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $collectionsElement = $structureManager->getElementByMarker('collections');
            $collectionsList = $structureManager->getElementsChildren($collectionsElement->id);

            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'collections', 'parent');

            foreach ($collectionsList as &$collection) {
                if (isset($compiledLinks[$collection->id]) && !in_array($collection->id, $structureElement->collections) && !$structureElement->connectAll
                ) {
                    $compiledLinks[$collection->id]->delete();
                } elseif (!isset($compiledLinks[$collection->id]) && ($structureElement->connectAll || in_array($collection->id, $structureElement->collections))
                ) {
                    $linksManager->linkElements($structureElement->id, $collection->id, 'collections');
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
            'collections',
            'columns',
            'content',
            'connectAll',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


