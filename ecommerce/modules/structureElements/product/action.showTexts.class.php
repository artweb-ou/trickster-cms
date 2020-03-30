<?php

class showTextsProduct extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('action', 'receiveTexts');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('texts'));

            $renderer->assign('contentList', $structureElement->getSubArticles());
        }
    }
}