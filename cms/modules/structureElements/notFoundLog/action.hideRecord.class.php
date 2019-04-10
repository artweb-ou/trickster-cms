<?php

class hideRecordNotFoundLog extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $recordId = $controller->getParameter('recordId');
        $db = $this->getService('db');
        $updated = $db->table('404_log')->whereId($recordId)->update(['hidden' => 1]);
        $structureElement->executeAction("show");
    }
}