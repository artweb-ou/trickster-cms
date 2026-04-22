<?php

class showFormNewsMailsGroup extends structureElementAction
{
    /**
     * @param newsMailsGroupElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $renderer->assign('pager', $structureElement->getPager());
            $renderer->assign('contentList', $structureElement->getEmailAddresses());
            $renderer->assign('actionButtons', $structureElement->getActionButtons());
            $renderer->assign('formElement', $structureElement);
        }
    }
}