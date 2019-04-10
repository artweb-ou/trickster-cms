<?php

class downloadPDFOrders extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->prepareSettingsData();

        if ($pdfContent = $structureElement->generatePDF()) {
            $httpResponse = CmsHttpResponse::getInstance();
            $httpResponse->setContentDisposition('attachment; filename="' . $structureElement->PDFFileName . '"');
            $httpResponse->setContentType('application/pdf');
            $httpResponse->sendHeaders();

            echo $pdfContent;
            exit();
        }
    }
}

