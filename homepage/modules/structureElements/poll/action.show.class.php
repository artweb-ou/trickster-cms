<?php

class showPoll extends structureElementAction
{
    /**
     * @param pollElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->currentIpHasVoted()) {
            $structureElement->setTemplate('poll.results.tpl');
        } else {
            $structureElement->setTemplate('poll.show.tpl');
        }
    }
}

