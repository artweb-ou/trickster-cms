<?php

class showFilesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'shared.files.tpl');
            //todo: investigate what is getAllConnectedDiscounts doing in shared template?
            //            $renderer->assign('connectedDiscounts', $structureElement->getAllConnectedDiscounts());
        }
    }
}