<?php

class showNewsMailForm extends structureElementAction
{
    /**
     * @param newsMailFormElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');

        $renderer = $this->getService(renderer::class);
        $renderer->assign('newsMailForm', $structureElement);
    }
}