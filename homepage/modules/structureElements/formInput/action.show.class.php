<?php

class showFormInput extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');
        $structureElement->dataChunk = 'text';
    }
}

