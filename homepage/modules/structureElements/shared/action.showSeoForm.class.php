<?php

class showSeoFormShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            if(!empty($structureElement->getMultiLanguageFields())) {
                $renderer->assign('form', $structureElement->getForm('multiLanguageSeo'));
            } else {
                $renderer->assign('form', $structureElement->getForm('singleLanguageSeo'));
            }
            $renderer->assign('action', 'receiveSeo');
        }
    }
}