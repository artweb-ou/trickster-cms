<?php

class showFullListTranslationsExport extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate("shared.content.tpl");
            $renderer = $this->getService('renderer');

            $renderer->assign('start', $controller->getParameter('start'));
            $renderer->assign('end', $controller->getParameter('end'));
            $renderer->assign('admin_translations', $controller->getParameter('admin_translations'));
            $renderer->assign("contentSubTemplate", "translationsExport.list.tpl");
        }
    }
}

