<?php

class showFormPollPlaceholder extends structureElementAction
{
    /**
     * @param pollPlaceholderElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->requested) {
            if ($pollsElement = $structureManager->getElementByMarker('polls')) {
                if ($pollsList = $structureManager->getElementsChildren($pollsElement->id)) {
                    $structureElement->pollsList = [];
                    foreach ($pollsList as $poll) {
                        $item = [];
                        $item['id'] = $poll->id;
                        $item['title'] = $poll->getTitle();
                        $item['select'] = $poll->id == $structureElement->pollId;
                        $structureElement->pollsList[] = $item;
                    }
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService(renderer::class);
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}