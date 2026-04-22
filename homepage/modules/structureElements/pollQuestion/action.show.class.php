<?php

class showPollQuestion extends structureElementAction
{
    /**
     * @param pollQuestionElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setTemplate('pollQuestion.show.tpl');
    }
}

