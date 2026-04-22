<?php

class showFormFileInput extends structureElementAction
{
    /**
     * @param formFileInputElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        $structureElement->dataChunk = 'files';
    }
}

