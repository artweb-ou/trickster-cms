<?php

class showFormSubArticle extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $form = $structureElement->getForm('form');
            $form->setStructure($structureElement->getFormStructure());
            $renderer->assign('form', $form);
        }
    }
}