<?php

/**
 * @var $genericIcon genericIconElement
 * @var $form ElementForm
 */
class showIconFormShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('action', 'receiveIcon');
            $renderer->assign('form', $structureElement->getForm('icon'));
        }
    }
}