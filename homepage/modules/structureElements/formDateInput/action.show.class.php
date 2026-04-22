<?php

class showFormDateInput extends structureElementAction
{
    /**
     * @param formDateInputElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        $structureElement->dataChunk = 'text';
    }
}

