<?php

class showBannerCategory extends structureElementAction
{
    /**
     * @param bannerCategoryElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $banners = $structureElement->getBannersToDisplay();
        if ($banners) {
            foreach ($banners as &$banner) {
                $banner->recordView();
            }
        }
        $structureElement->setViewName('show');
    }
}


