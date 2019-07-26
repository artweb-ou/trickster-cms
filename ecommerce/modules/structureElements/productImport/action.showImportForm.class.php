<?php

class showImportFormProductImport extends structureElementAction
{
    protected $actionsLogData;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $languagesManager = $this->getService('LanguagesManager');
            $renderer = $this->getService('renderer');
            $x = $structureElement->categoryId;
            if ($structureElement->categoryId) {
                $renderer->assign('category', $structureManager->getElementById($structureElement->categoryId));
            }
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            $languagesList = $languagesManager->getLanguagesList($marker);
            $structureElement->setTemplate('shared.content.tpl');
            $renderer->assign('languagesList', $languagesList);
            $renderer->assign('contentSubTemplate', 'productImport.tpl');
        }
    }
}