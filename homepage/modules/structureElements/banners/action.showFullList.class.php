<?php

class showFullListBanners extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'banners.list.tpl');
            }
        }
    }
}
