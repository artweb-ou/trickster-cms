<?php

class showFormSelect extends structureElementAction
{
    /**
     * @param formSelectElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        $structureElement->dataChunk = 'text';
    }
}

