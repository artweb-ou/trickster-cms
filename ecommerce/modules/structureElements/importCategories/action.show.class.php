<?php

class showImportCategories extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $importPlugins = [];
            $categories = [];
            $selectedPlugin = null;
            $pluginsFolder = $structureManager->getElementByMarker('importPlugins');
            if ($pluginsFolder) {
                $importPlugins = $pluginsFolder->getChildrenList();
            }
            if ($pluginId = $controller->getParameter('plugin')) {
                foreach ($importPlugins as &$plugin) {
                    if ($plugin->id == $pluginId) {
                        $selectedPlugin = $plugin;
                        break;
                    }
                }
            } elseif ($importPlugins) {
                $selectedPlugin = $importPlugins[0];
            }
            $categoriesElement = $structureManager->getElementByMarker('categories');
            if ($categoriesElement) {
                $categoriesElementChildren = $categoriesElement->getChildrenList();
                $categories = $this->collectTreeCategories($categoriesElementChildren);
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'importCategories.show.tpl');
            $renderer->assign('importPlugins', $importPlugins);
            $renderer->assign('selectedPlugin', $selectedPlugin);
            $renderer->assign('categories', $categories);
        }
    }

    protected function collectTreeCategories($parentCategories)
    {
        $categories = [];
        foreach ($parentCategories as &$parentCategory) {
            $categories[] = $parentCategory;
            if ($children = $parentCategory->getChildrenList()) {
                $categories = array_merge($categories, $this->collectTreeCategories($children));
            }
        }
        return $categories;
    }
}


