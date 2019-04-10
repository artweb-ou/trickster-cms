<?php

class showErrorPage extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('errorPage.show.tpl');
    }
}