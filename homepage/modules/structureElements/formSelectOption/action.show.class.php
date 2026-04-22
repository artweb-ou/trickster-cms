<?php

class showFormSelectOption extends structureElementAction
{
    /**
     * @param formSelectOptionElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        $structureElement->dataChunk = 'text';
    }
}

