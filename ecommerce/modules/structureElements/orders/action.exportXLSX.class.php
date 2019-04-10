<?php

class exportXLSXOrders extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->prepareSettingsData();
        $xlsxPaths = $structureElement->generateXLSX();
        if ($xlsxPaths['filePath']) {
            $httpResponse = CmsHttpResponse::getInstance();
            $httpResponse->setContentDisposition('attachment; filename="' . $structureElement->XLSXFileName . '"');
            $httpResponse->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $httpResponse->sendHeaders();
            readfile($xlsxPaths['filePath']);

            unlink($xlsxPaths['filePath']);
            rmdir($xlsxPaths['workspace']);
            $pathsManager = $this->getService('PathsManager');
            rmdir($pathsManager->getPath('temporary') . 'orders_exports');

            exit();
        }
    }
}


