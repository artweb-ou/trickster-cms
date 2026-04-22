<?php

class deleteFileNewsList extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsListElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
//        $errorLog = errorLog::getInstance();
//        $errorLog->logMessage(__CLASS__,'image is: '. $controller->getParameter('file'));
        $deletedFile = $controller->getParameter('file');

        if ($structureElement->$deletedFile) {
            $structureElement->$deletedFile = '';
            $deletedFileOriginalName = $deletedFile .'OriginalName';
            $structureElement->$deletedFileOriginalName = '';

            $structureElement->persistElementData();
        }
        $controller->redirect($structureElement->getUrl('showLayoutForm'));
    }
}
