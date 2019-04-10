<?php

class showPoll extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->currentIpHasVoted()) {
            $structureElement->setTemplate('poll.results.tpl');
        } else {
            $structureElement->setTemplate('poll.show.tpl');
        }
    }
}

