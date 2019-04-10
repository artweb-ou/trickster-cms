<?php

class showImportFormNewsMailsAddresses extends structureElementAction
{
    protected $actionsLogData;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $renderer = $this->getService('renderer');

            $structureElement->setTemplate('shared.content.tpl');
            $renderer->assign('contentSubTemplate', 'newsMailsAddresses.import.tpl');
        }
    }
}