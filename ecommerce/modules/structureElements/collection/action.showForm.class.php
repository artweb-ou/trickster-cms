<?php

class showFormCollection extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->collectionsListIds = [];
            $connectedCollectionsListsIds = $structureElement->getConnectedBrandsListsIds();
            if ($collectionsListElements = $structureManager->getElementsByType('collectionsList')) {
                foreach ($collectionsListElements as &$collectionsListElement) {
                    if ($collectionsListElement->connectAll) {
                        continue;
                    }
                    $item = [];
                    $item['id'] = $collectionsListElement->id;
                    $item['title'] = $collectionsListElement->getTitle();
                    $item['select'] = in_array($collectionsListElement->id, $connectedCollectionsListsIds);
                    $structureElement->collectionsListIds[] = $item;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}