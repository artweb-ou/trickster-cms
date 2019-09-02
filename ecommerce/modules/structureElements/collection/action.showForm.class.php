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
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}