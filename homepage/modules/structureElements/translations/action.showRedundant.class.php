<?php

class showRedundantTranslations extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate("shared.content.tpl");
            $renderer = $this->getService('renderer');
            $renderer->assign("contentSubTemplate", "translations.list_redundant.tpl");
        }
    }
}

