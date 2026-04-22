<?php

class showFormShortcut extends structureElementAction
{
    /**
     * @param shortcutElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
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
        $renderer = $this->getService(renderer::class);
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('form', $structureElement->getForm('form'));
    }
}