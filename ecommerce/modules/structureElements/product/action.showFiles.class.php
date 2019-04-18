<?php

class showFilesProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('form', $structureElement->getForm('files'));
            $renderer->assign('action', 'receiveFiles');
            $renderer->assign('connectedDiscounts', $structureElement->getAllConnectedDiscounts());
            if ($linksManager = $structureElement->getService('linksManager')) {
                $connectedFileLinks = $linksManager->getElementsLinks($structureElement->id, 'connectedFile', 'parent');
                foreach ($connectedFileLinks as &$link) {
                    if ($fileElement = $structureManager->getElementById($link->childStructureId)) {
                        $contentList[] = $fileElement;
                    }
                }
            }
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
        }
    }
}