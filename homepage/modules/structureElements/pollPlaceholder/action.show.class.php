<?php

class showPollPlaceholder extends structureElementAction
{
    /**
     * @param pollPlaceholderElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setTemplate('pollPlaceholder.column.tpl');
    }
}

