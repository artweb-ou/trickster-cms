<?php

class showActionsLog extends structureElementAction
{
    protected $actionsLogData;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'actionsLog.tpl');
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'elementId',
            'elementType',
            'elementName',
            'periodStart',
            'periodEnd',
            'userId',
            'userIP',
            'action',
        ];
    }
}