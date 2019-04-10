<?php

class xlsExportCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $pathsManager = $this->getService('PathsManager');
            $dir = $pathsManager->getPath('temporary');
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $exportId = uniqid();

            $dir = $pathsManager->getPath('temporary') . 'products_exports/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $exportsDir = $dir;
            $dir = $exportsDir . $exportId . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $translationsManager = $this->getService('translationsManager');
            $translations = $translationsManager->getTranslationsList('adminTranslations');

            $workspaceDir = $dir;

            $header = [
                $translations['label.name'] => 'string',
                $translations['field.code'] => 'string',
                $translations['field.price'] => 'euro',
                $translations['productslist.availability'] => 'string',
                $translations['label.category'] => 'string',
                $translations['label.date'] => 'string',
            ];

            $excelFile = 'products.xlsx';
            $writer = new XLSXWriter();
            $writer->writeSheetHeader('Sheet1', $header);

            foreach ($structureElement->getProductsPage(1000000) as $key => $product) {
                $availability = '';
                if ($product->availability == 'available') {
                    $availability = $translations['label.available'];
                } elseif ($product->availability == 'quantity_dependent') {
                    $availability = $translations['label.quantity_dependent'];
                } elseif ($product->availability == 'inquirable') {
                    $availability = $translations['label.inquirable'];
                } elseif ($product->availability == 'unavailable') {
                    $availability = $translations['label.unavailable'];
                }

                $categories = [];
                foreach ($product->getConnectedAdminCategories() as $category) {
                    $categories[] = $category->getTitle() . ' ';
                }

                $data = [
                    $product->getTitle(),
                    $product->code,
                    $product->price,
                    $availability,
                    implode(', ', $categories),
                    $product->dateModified,
                ];
                $writer->writeSheetRow('Sheet1', $data);
            }

            $writer->writeToFile($workspaceDir . $excelFile);

            $xlsxPaths = [
                'filePath' => ($workspaceDir . $excelFile),
                'workspace' => $workspaceDir,
            ];

            if ($xlsxPaths['filePath']) {
                $httpResponse = CmsHttpResponse::getInstance();
                $httpResponse->setContentDisposition('attachment; filename="' . $excelFile . '"');
                $httpResponse->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $httpResponse->sendHeaders();
                readfile($xlsxPaths['filePath']);

                unlink($xlsxPaths['filePath']);
                rmdir($xlsxPaths['workspace']);
                $pathsManager = $this->getService('PathsManager');
                rmdir($pathsManager->getPath('temporary') . 'products_exports');

                exit();
            }
        }
    }
}

