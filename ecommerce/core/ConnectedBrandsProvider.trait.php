<?php

trait ConnectedBrandsProviderTrait
{
    protected $connectedBrands;
    protected $connectedBrandsIds;

    public function getConnectedBrands()
    {
        if ($this->connectedBrands === null) {
            $this->connectedBrands = [];
            if ($brandIds = $this->getConnectedBrandsIds()) {
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($brandIds as &$brandId) {
                    if ($brandId && $brandElement = $structureManager->getElementById($brandId)) {
                        //                        $this->connectedBrands[] = $brandElement;
                        $item = [];
                        $item['id'] = $brandElement->id;
                        $item['title'] = $brandElement->getTitle();
                        $item['select'] = true;
                        $this->connectedBrands[] = $item;
                    }
                }
            }
        }
        return $this->connectedBrands;
    }

    public function getConnectedBrandsIds()
    {
        if ($this->connectedBrandsIds === null) {
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');
            $this->connectedBrandsIds = $linksManager->getConnectedIdList($this->id, $this->structureType . "Brand", "parent");
        }
        return $this->connectedBrandsIds;
    }

    public function updateConnectedBrands($formBrands)
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check brand links
        if ($connectedBrandsIds = $this->getConnectedBrandsIds()) {
            foreach ($connectedBrandsIds as &$connectedBrandId) {
                if (!in_array($connectedBrandId, $formBrands)) {
                    $linksManager->unLinkElements($this->id, $connectedBrandId, $this->structureType . 'Brand');
                }
            }
        }
        foreach ($formBrands as $selectedBrandId) {
            $linksManager->linkElements($this->id, $selectedBrandId, $this->structureType . 'Brand');
        }
        $this->connectedBrandsIds = null;
    }
}