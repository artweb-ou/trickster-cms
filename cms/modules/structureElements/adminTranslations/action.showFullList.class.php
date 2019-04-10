<?php

class showFullListAdminTranslations extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate("shared.content.tpl");
            $renderer = $this->getService('renderer');
            if ($controller->getParameter("incomplete")) {
                $renderer->assign("contentSubTemplate", "translations.list_incomplete.tpl");
            } else {
                $renderer->assign("contentSubTemplate", "translations.list.tpl");
            }
        }
    }
}