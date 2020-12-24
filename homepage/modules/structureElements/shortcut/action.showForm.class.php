<?php

class showFormShortcut extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $structureElement->elementsList = [];
            $elementsList = $structureManager->getElementsFlatTree($structureManager->getRootElementId(), 'container');

            foreach ($elementsList as $element) {
                $flatItem = [];
                $flatItem['level'] = $element->level;
                $flatItem['id'] = $element->id;
                $flatItem['title'] = $element->getTitle();
                $flatItem['select'] = $structureElement->target == $element->id;

                $structureElement->elementsList[] = $flatItem;
            }
        }

        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('form', $structureElement->getForm('form'));
    }
}