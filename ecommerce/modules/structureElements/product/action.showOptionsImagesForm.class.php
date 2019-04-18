<?php

class showOptionsImagesFormProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'product.options_images_form.tpl');
    }
}
