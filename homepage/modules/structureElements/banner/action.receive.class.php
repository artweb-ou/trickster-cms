<?php

class receiveBanner extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param bannerElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;

                if ($structureElement->getDataChunk("image")->mime == 'application/x-shockwave-flash') {
                    $structureElement->type = 'flash';
                } else {
                    $structureElement->type = 'image';
                }
            }
            $structureElement->persistElementData();

            // check category links
            $linksManager = $this->getService('linksManager');
            if ($connectedCategoryIds = $structureElement->getConnectedCategoriesIds()) {
                foreach ($connectedCategoryIds as &$connectedCategoryId) {
                    if (!in_array($connectedCategoryId, $structureElement->bannerCategoryIds)) {
                        $linksManager->unLinkElements($connectedCategoryId, $structureElement->id, "bannerCategoryBanner");
                    }
                }
            }
            foreach ($structureElement->bannerCategoryIds as $selectedCategoryId) {
                $linksManager->linkElements($selectedCategoryId, $structureElement->id, "bannerCategoryBanner");
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setValidators(&$validators)
    {
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'width',
            'height',
            'image',
            'link',
            'clickTag',
            'bannerCategoryIds',
            'openInNewWindow',
        ];
    }
}


