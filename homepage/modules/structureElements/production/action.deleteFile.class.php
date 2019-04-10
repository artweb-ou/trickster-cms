<?php

class deleteFileProduction extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
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

