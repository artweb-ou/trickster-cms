<?php

class showFullListLanguage extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested === true) {
            $contentType = 'structure';
            if ($controller->getApplicationName() != 'adminAjax') {
                if ($controller->getParameter('view')) {
                    $contentType = $controller->getParameter('view');
                }
            }
            $structureManager->setNewElementLinkType($contentType);
            $structureManager->getElementsChildren($structureElement->id);

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'shared.contentlist.tpl');
                $renderer->assign('contentType', $contentType);
            }
        }
    }
}