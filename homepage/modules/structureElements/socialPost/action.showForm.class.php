<?php

class showFormSocialPost extends structureElementAction
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

//        if ($socialPostElement = $structureElement->getSearchTypesString('admin')) {
//            $content['id'] = $socialPostElement->id;
//            $content['title'] = $socialPostElement->userName;
//        }

        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}