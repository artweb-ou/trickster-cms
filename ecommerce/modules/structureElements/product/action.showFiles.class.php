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
            $contentList = $structureElement->getFilesList();
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
        }
    }
}