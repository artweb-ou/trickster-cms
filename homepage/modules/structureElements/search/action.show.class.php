<?php

class showSearch extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');

        $renderer = $this->getService('renderer');
        $renderer->assign('searchFormElement', $structureElement);

        $structureElement->setViewName('result');
    }
}

