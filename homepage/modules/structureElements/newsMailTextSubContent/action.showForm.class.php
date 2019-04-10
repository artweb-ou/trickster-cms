<?php

class showFormNewsMailTextSubContent extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();

            $selectedCategory = $structureElement->getConnectedCategory();
            $structureElement->subContentCategories = array();
            if ($newsMailElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                foreach ($newsMailElement->getCategoryElements() as $element) {
                    $category['id'] = $element->id;
                    $category['title'] = $element->getTitle();
                    $category['select'] = false;
                    if ($selectedCategory->id == $element->id) {
                        $category['select'] = true;
                    }
                    $structureElement->subContentCategories[] = $category;
                }
            }
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}
