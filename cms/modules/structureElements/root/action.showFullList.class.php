<?php

class showFullListRoot extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $adminRoot = $structureElement->isAdminRoot();
        if ($adminRoot) {
            $languageNames = [];

            //init login form
            $structureElement->getChildrenList();
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            if ($publicRoot = $structureManager->getElementByMarker($marker)) {
                $childElements = $publicRoot->getChildrenList();
                foreach ($childElements as &$element) {
                    if ($element->structureType == 'language') {
                        $languageNames[$element->id] = $element->title;
                    }
                }
            }

            if ($adminLanguages = $structureManager->getElementByMarker('adminLanguages')) {
                $structureManager->getElementsChildren($adminLanguages->id);
                $childElements = $adminLanguages->getChildrenList();
                foreach ($childElements as &$element) {
                    if ($element->structureType == 'language') {
                        $languageNames[$element->id] = $element->title;
                    }
                }
            }
            $renderer = $this->getService('renderer');
            $renderer->assign('languageNames', $languageNames);
        }

        if ($structureElement->final) {
            $renderer = $this->getService('renderer');
            $currentView = $controller->getParameter('view');
            if (!$currentView) {
                $currentView = $adminRoot ? 'dashboard' : 'list';
            }
            $contentSubTemplate = 'shared.contentlist.tpl';
            $dashboard = null;
            if ($adminRoot && $currentView === 'dashboard') {
                $contentSubTemplate = 'dashboard.tpl';
                $dashboard = new Dashboard();
                $dashboard->setStructureManager($structureManager);
                $dashboard->setDb($this->getService('db'));
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer->assign('dashboard', $dashboard);
            $renderer->assign('contentSubTemplate', $contentSubTemplate);
            $renderer->assign('view', $currentView);
        }
    }
}