<?php

class showImportCalculations extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $categories = [];
            $importPlugins = [];
            $existingModifiersIndex = [];
            $pluginsFolder = $structureManager->getElementByMarker('importPlugins');
            if ($pluginsFolder) {
                $importPlugins = $pluginsFolder->getChildrenList();
            }
            if ($pluginsFolder && $importPlugins) {
                if ($categoriesMenu = $structureManager->getElementByMarker('categories')) {
                    $categories = $structureManager->getElementsFlatTree($categoriesMenu->id);
                }
            }
            if ($categories) {
                $existingModifiersIndex = $structureElement->getCategoryPluginIndex();
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'importCalculations.show.tpl');
            $renderer->assign('categories', $categories);
            $renderer->assign('importPlugins', $importPlugins);
            $renderer->assign('modifiersIndex', $existingModifiersIndex);
        }
    }
}


