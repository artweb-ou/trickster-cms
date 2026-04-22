<?php

class deleteFileProduction extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param productionElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $language = false;
        if ($controller->getParameter('language')) {
            $language = $controller->getParameter('language');
        }

        $structureElement->setViewName('form');
        if ($structureElement->file) {
            foreach ($structureElement->getAllDataChunks() as $langId => $chunks) {
                if (($language !== false && $language == $langId) || $language === false) {
                    $chunks['file']->deleteExtraData();
                    $structureElement->setValue('file', '', $langId);
                    $structureElement->setValue('originalName2', '', $langId);
                }
            }
            $structureElement->persistElementData();
        }
        $controller->restart($structureElement->URL);
    }
}

