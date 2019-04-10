<?php

class importProductImport extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        ini_set("max_execution_time", 60 * 60 * 10);
        if ($this->validated) {
            if (($structureElement->getDataChunk("importFile")->originalName) !== null) {
                $template = $structureManager->getElementById($structureElement->templateId);
                $templateData = $template->getTemplateData();
                $structureElement->importFile = $structureElement->getDataChunk("importFile")->originalName;

                $importManager = $this->getService('CsvImportManager');
                $importManager->setTemplateColumns($templateData['columns']);
                $importManager->setImportOrigin($templateData['importOrigin']);
                $importManager->setPriceAdjustment($templateData['priceAdjustment']);
                $importManager->setDelimiter($templateData['delimiter']);
                if ($templateData['ignoreFirstRow']) {
                    $importManager->disableFirstRowImport();
                }
                $importManager->setFile($structureElement->getDataChunk("importFile")->temporaryName);
                $importManager->setCategoryId($structureElement->categoryId);
                $importManager->setLanguageCode($structureElement->languageCode);
                if ($importManager->checkFile()) {
                    $importManager->import();
                } else {
                    $renderer = $this->getService('renderer');
                    $renderer->assign('importErrorCode', $importManager->getErrorCode());
                    $renderer->assign('errorLocationLineNumber', $importManager->getErrorLocationLineNumber());
                    $renderer->assign('errorFieldName', $importManager->getErrorFieldName());
                    $renderer->assign('errorArgument', $importManager->getErrorArgument());
                }
            }
        }
        $structureElement->executeAction("showImportForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'categoryId',
            'languageCode',
            'importFile',
            'templateId',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['importFile'][] = 'notEmpty';
    }
}


