<?php

class showPrivilegesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->privilegesForm = $structureManager->createElement('privileges', 'showRelations', $structureElement->id)
        ) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'shared.privileges.tpl');
        }
    }
}