<?php

class receiveProductParameter extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
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
                        $linksManager->unLinkElements($connectedId, $structureElement->id, 'categoryParameter');
                    }
                }
            }
            foreach ($structureElement->categoriesIds as &$idToConnect) {
                if (!in_array($idToConnect, $connectedIds)) {
                    $linksManager->linkElements($idToConnect, $structureElement->id, 'categoryParameter');
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


