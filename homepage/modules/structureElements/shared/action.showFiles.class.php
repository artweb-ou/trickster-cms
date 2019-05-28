<?php

class showFilesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('form', $structureElement->getForm('files'));
            $renderer->assign('action', 'receiveFiles');
            $contentList = $structureElement->getFilesList();
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
        }
    }
}