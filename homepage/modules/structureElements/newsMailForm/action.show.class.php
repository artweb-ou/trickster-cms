<?php

class showNewsMailForm extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');

        $renderer = $this->getService('renderer');
        $renderer->assign('newsMailForm', $structureElement);
    }
}