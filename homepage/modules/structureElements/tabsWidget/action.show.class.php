<?php

class showTabsWidget extends structureElementAction
{
    /**
     * @param tabsWidgetElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('show');
    }
}

