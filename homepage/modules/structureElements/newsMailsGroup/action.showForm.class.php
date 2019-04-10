<?php

class showFormNewsMailsGroup extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $renderer->assign('pager', $structureElement->getPager());
            $renderer->assign('contentList', $structureElement->getEmailAddresses());
            $renderer->assign('actionButtons', $structureElement->getActionButtons());
            $renderer->assign('formElement', $structureElement);
        }
    }
}