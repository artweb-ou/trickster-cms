<?php

class showPollAnswer extends structureElementAction
{
    /**
     * @param pollAnswerElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setTemplate('pollAnswer.show.tpl');
    }
}

