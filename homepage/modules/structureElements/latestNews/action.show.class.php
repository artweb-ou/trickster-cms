<?php

class showLatestNews extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->newsViewType == 'big' || $structureElement->newsViewType == 'small') {
            $structureElement->setViewName('column');
        } else {
            $structureElement->setViewName('show');
        }
    }
}