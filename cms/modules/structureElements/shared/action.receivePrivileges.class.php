<?php

class receivePrivilegesShared extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->privilegesForm = $structureManager->createElement('privileges', 'receiveRelations', $structureElement->id)
        ) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'shared.privileges.tpl');
        }
    }
}
