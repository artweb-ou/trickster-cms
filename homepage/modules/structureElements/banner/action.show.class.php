<?php

class showBanner extends structureElementAction
{
    /**
     * @param bannerElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('show');
    }
}
