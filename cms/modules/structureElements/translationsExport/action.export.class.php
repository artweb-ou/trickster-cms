<?php

class exportTranslationsExport extends structureElementAction
{
    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'elements',
        ];
    }

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->executeAction('showFullList');
        $isAdminTranslations = $controller->getParameter('admin_translations');
        $exportData = [];

        if ($this->validated) {
            $elements = $structureElement->elements;
            foreach ($elements as $groupId => $childrensValues) {
                foreach ($childrensValues as $elementID => &$value) {
                    if ($exportElement = $structureManager->getElementById($elementID)) {
                        $exportElement->parentTitle = $structureManager->getElementById($groupId)->title;
                        $exportData[] = $exportElement;
                    }
                }
            }
        }

        $renderer = $this->getService('renderer');
        $renderer->assign('exportData', $exportData);

        if ($isAdminTranslations) {
            $renderer->assign('translationsType', 'adminTranslations');
        } else {
            $renderer->assign('translationsType', 'public_translations');
        }

        $languageCodes = [];
        if ($isAdminTranslations) {
            if ($adminLanguages = $structureManager->getElementByMarker('adminLanguages')) {
                $structureManager->getElementsChildren($adminLanguages->id);
                $childElements = $adminLanguages->getChildrenList();
                foreach ($childElements as $element) {
                    if ($element->structureType == 'language') {
                        $languageCodes[$element->id] = $element->iso6393;
                    }
                }
            }
        } else {
            $languagesManager = $this->getService('LanguagesManager');
            $languagesList = $languagesManager->getLanguagesList();
            foreach ($languagesList as $languagesItem) {
                $languageCodes[$languagesItem->id] = $languagesItem->iso6393;
            }
        }

        $renderer->assign('languagesList', $languageCodes);

        $path = $this->getService('PathsManager')->getPath('trickster');
        $renderer->setTemplatesFolder($path . 'cms/templates/xml');
        $renderer->setTemplate('xml.translations.tpl');

        $renderer->setCacheControl('no-cache');
        $renderer->setContentDisposition('attachment');
        $renderer->setContentType('application/xml');
        $renderer->display();
        exit;
    }
}

