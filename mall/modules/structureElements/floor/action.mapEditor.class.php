<?php

class mapEditorFloor extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $renderer = renderer::getInstance();

        $designThemesManager = $this->getService('DesignThemesManager', ['currentThemeCode' => 'admin']);
        $currentTheme = $designThemesManager->getCurrentTheme();

        $renderer->assign('nodesInfo', '');
        $renderer->assign('theme', $currentTheme);
        $renderer->assign('controller', $controller);
        $renderer->assign('element', $structureElement);
        $renderer->template = $currentTheme->template('floor.mapEditor.tpl');
        $renderer->display();
        exit;
    }
}

