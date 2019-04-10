<?php

class showPollAnswer extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('pollAnswer.show.tpl');
    }
}

