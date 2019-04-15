<?php

class showFormShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $contentList = $structureElement->getChildrenList();
            foreach ($contentList as $key => $contentItem) {
                if (!in_array($contentItem->structureType, $structureElement->getAllowedTypes())) {
                    unset($contentList[$key]);
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentList', $contentList);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $structureElement->logViewEvent();
        }
    }
}