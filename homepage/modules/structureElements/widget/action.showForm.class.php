<?php

class showFormWidget extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        //		$structureElement->setViewName('form');
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}