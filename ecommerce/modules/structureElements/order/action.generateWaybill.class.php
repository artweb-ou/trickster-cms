<?php

class generateWaybillOrder extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param orderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $filename = "waybill_".$structureElement->orderNumber.".pdf";
        $content = $structureElement->makeWaybillPdf();
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/force-download');
        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Content-Transfer-Encoding: binary');
        echo $content;
        exit;
    }
}

