<?php

class deleteFileNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailsTextElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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

