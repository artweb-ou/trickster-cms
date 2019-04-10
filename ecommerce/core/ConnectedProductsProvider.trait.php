<?php

trait ConnectedProductsProviderTrait
{
    protected $cppt_connectedProductsIds;
    protected $cppt_connectedProducts;

    public function getConnectedProducts()
    {
        if ($this->cppt_connectedProducts === null) {
            $this->cppt_connectedProducts = [];
            if ($productIds = $this->getConnectedProductsIds()) {
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($productIds as &$productId) {
                    if ($productId && $productElement = $structureManager->getElementById($productId)) {
                        //                        $this->cppt_connectedProducts[] = $productElement;
                        $item = [];
                        $item['id'] = $productElement->id;
                        $item['title'] = $productElement->getTitle();
                        $item['select'] = true;
                        $this->cppt_connectedProducts[] = $item;
                    }
                }
            }
        }
        return $this->cppt_connectedProducts;
    }

    public function getConnectedProductsIds()
    {
        if ($this->cppt_connectedProductsIds === null) {
            $this->cppt_connectedProductsIds = [];
            if ($this->hasActualStructureInfo()) {
                /**
                 * @var linksManager $linksManager
                 */
                $linksManager = $this->getService('linksManager');
                $this->cppt_connectedProductsIds = $linksManager->getConnectedIdList($this->id, $this->structureType . "Product", "parent");
            }
        }
        return $this->cppt_connectedProductsIds;
    }

    public function updateConnectedProducts($formProducts)
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check product links
        if ($connectedProductsIds = $this->getConnectedProductsIds()) {
            foreach ($connectedProductsIds as &$connectedProductId) {
                if (!in_array($connectedProductId, $formProducts)) {
                    $linksManager->unLinkElements($this->id, $connectedProductId, $this->structureType . 'Product');
                }
            }
        }
        foreach ($formProducts as $selectedProductId) {
            $linksManager->linkElements($this->id, $selectedProductId, $this->structureType . 'Product');
        }
        $this->cppt_connectedProducts = null;
        $this->cppt_connectedProductsIds = null;
    }
}