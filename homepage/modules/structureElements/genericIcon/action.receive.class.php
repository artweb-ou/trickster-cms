<?php

class receiveGenericIcon extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            foreach ($structureElement->getMultilanguageDataChunk('image') as $languageId => $dataChunk) {
                if ($dataChunk->originalName) {
                    $structureElement->setValue('image', $structureElement->id . '_' . $languageId, $languageId);
                    $structureElement->setValue('originalName', $dataChunk->originalName, $languageId);
                }
            }

            $structureElement->persistElementData();

            $structureElement->updateConnectedProducts($structureElement->products);
            $structureElement->updateConnectedCategories($structureElement->categories);
            $structureElement->updateConnectedBrands($structureElement->brands);
//            $structureElement->updateConnectedParameters($structureElement->parameters);

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



            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'image',
            'title',
            'products',
            'categories',
            'categories',
            'brands',
            'startDate',
            'endDate',
            'days',
            'iconWidth',
            'iconLocation',
            'iconRole',
            'iconProductAvail',
            'parametersIds',
        ];
    }
}