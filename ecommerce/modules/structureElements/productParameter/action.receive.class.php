<?php

class receiveProductParameter extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productParameterElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->getId();
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }

            $structureElement->persistElementData();

            $linksManager = $this->getService('linksManager');
            if ($connectedIds = $structureElement->getConnectedCategoriesIds()) {
                foreach ($connectedIds as $connectedId) {
                    if (!in_array($connectedId, $structureElement->categoriesIds)) {
                        $linksManager->unLinkElements($connectedId, $structureElement->getId(), 'categoryParameter');
                    }
                }
            }
            foreach ($structureElement->categoriesIds as $idToConnect) {
                if (!in_array($idToConnect, $connectedIds)) {
                    $linksManager->linkElements($idToConnect, $structureElement->getId(), 'categoryParameter');
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
            'structureName',
            'image',
            'single',
            'primary',
            'categoriesIds',
            'hint',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['title'][] = 'notEmpty';
    }
}


