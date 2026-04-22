<?php

class showLatestNews extends structureElementAction
{
    /**
     * @param latestNewsElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->newsViewType == 'big' || $structureElement->newsViewType == 'small') {
            $structureElement->setViewName('column');
        } else {
            $structureElement->setViewName('show');
        }
    }
}