<?php

class showFormCollection extends structureElementAction
{

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param collectionElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var $linksManager linksManager
         */
        if ($structureElement->final) {
            $structureElement->collectionsListsList = [];
            $connectedCollectionsListsIds = $structureElement->getConnectedCollectionsListsIds();
            if ($collectionsListElements = $structureManager->getElementsByType('collectionsList')) {
                foreach ($collectionsListElements as &$collectionsListElement) {
                    $item = [];
                    $item['id'] = $collectionsListElement->id;
                    $item['title'] = $collectionsListElement->getTitle();
                    $item['select'] = in_array($collectionsListElement->id, $connectedCollectionsListsIds);
                    $structureElement->collectionsListsList[] = $item;
                }
            }
            $connectedProducts = [];
            $linksManager = $this->getService('linksManager');
            $connectedProductIds = $linksManager->getConnectedIdList($structureElement->id, 'collectionProduct');
            foreach ($connectedProductIds as $productId) {
                $productElement = $structureManager->getElementById($productId);
                if($productElement) {
                    $connectedProducts[] = [
                        'id' => $productElement->id,
                        'title' => $productElement->getTitle(),
                        'select' => true
                    ];
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $structureElement->connectedProducts = $connectedProducts;
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}