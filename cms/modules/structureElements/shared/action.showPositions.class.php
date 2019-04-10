<?php

class showPositionsShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->positionsForm = $structureManager->createElement('positions', 'show', $structureElement->id)) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('positions'));
            $renderer->assign('action', 'receivePositions');
        }
    }
}