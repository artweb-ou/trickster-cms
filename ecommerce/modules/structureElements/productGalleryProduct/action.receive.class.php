<?php

class receiveProductGalleryProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            if (!is_null($structureElement->getDataChunk('image')->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk('image')->originalName;
            }
            $structureElement->persistElementData();

            $linksManager = $this->getService('linksManager');
            foreach ($structureElement->getConnectedProductsIds() as &$connectedId) {
                if (!in_array($connectedId, $structureElement->productIds)) {
                    $linksManager->unLinkElements($connectedId,
                        $structureElement->id,
                        $structureElement::LINK_TYPE_PRODUCT);
                }
            }
            foreach ($structureElement->productIds as $selectedTypeId) {
                $linksManager->linkElements($selectedTypeId,
                    $structureElement->id,
                    $structureElement::LINK_TYPE_PRODUCT);
            }
            if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->redirect($parentElement->getUrl('showProducts'));
            }
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'positionX',
            'positionY',
            'productIds',
            'image',
            'title',
            'description',
            'code',
            'price',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}