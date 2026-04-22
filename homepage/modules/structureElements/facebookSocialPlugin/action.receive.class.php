<?php

class receiveFacebookSocialPlugin extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param facebookSocialPluginElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!is_null($structureElement->getDataChunk("icon")->originalName)) {
                $structureElement->icon = $structureElement->id . "ico";
                $structureElement->iconOriginalName = $structureElement->getDataChunk("icon")->originalName;
            }
            $structureElement->importExternalData([]);
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'icon',
            'appId',
            'appKey',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

