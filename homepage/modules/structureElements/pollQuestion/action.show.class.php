<?php

class showPollQuestion extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('pollQuestion.show.tpl');
    }
}

