<?php

class deleteFileNewsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
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
