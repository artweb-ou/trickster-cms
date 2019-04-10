<?php

class showArticle extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $layoutField = $controller->getApplicationName() == 'mobile'
            ? 'mobileLayout' : 'layout';
        $layout = $structureElement->$layoutField;
        $application = $controller->getApplication();
        $theme = method_exists($application, 'getCurrentTheme')
            ? $application->getCurrentTheme() : null;
        if (!$layout || !$theme || !$theme->templateExists("article.$layout.tpl")) {
            $layout = 'show';
        }
        $structureElement->setViewName($layout);
        if ($structureElement->final) {
            if ($parent = $structureManager->getElementsFirstParent($structureElement->id)) {
                $controller->restart($parent->URL);
            }
        }
    }
}

