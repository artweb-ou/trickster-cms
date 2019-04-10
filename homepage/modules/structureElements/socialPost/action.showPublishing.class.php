<?php

class showPublishingSocialPost extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($controller->getParameter('elementId')) {
            $elementId = $controller->getParameter('elementId');
            if ($element = $structureManager->getElementById($elementId)) {
                $structureElement->linkTitle = $element->title;
                $structureElement->linkURL = $element->URL;
                $structureElement->linkDescription = $element->introduction;
            }
        }
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'socialPost.showPublishing.tpl');
        }
    }
}