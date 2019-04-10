<?php

class showBannerCategory extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
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


