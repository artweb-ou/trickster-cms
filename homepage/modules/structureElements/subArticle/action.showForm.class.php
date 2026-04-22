<?php

class showFormSubArticle extends structureElementAction
{
    /**
     * @param subArticleElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $form = $structureElement->getForm('form');
            $form->setStructure($structureElement->getFormStructure());
            $renderer->assign('form', $form);
        }
    }
}