<?php

class showCategory extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param categoryElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->logViewEvent();
        }
    }
}