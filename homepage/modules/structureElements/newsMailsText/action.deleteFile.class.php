<?php

class deleteFileNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');

        if ($structureElement->file) {
            foreach ($structureElement->getAllDataChunks() as $chunks) {
                $chunks['file']->deleteExtraData();
            }
            $structureElement->image = '';
            $structureElement->originalName = '';

            $structureElement->persistElementData();
        }
        $controller->restart($structureElement->URL);
    }
}

