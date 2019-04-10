<?php

class showRoot extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $pathSearchLinksBlacklist = [
                'connected',
                'connected2',
                'connected3',
                'connectedCategory',
                'displayinmenu',
                'displayinmenumobile',
                'selectedProducts',
                'submenulist',
                'userRelation',
                'productbrand',
                'discountCategory',
                'categoryParameter',
                'selectedProductsCategory',
                'latestNewsNewsList',
                'productSearchParameter',
                'productSearchCatalogue',
                'selectedProducts',
                'selectedNews',
                'foreignRelative',
                'discountCategory',
                'discountBrand',
                'discountProduct',
                'selectedProductsCatalogue',
                'newsMailTextSubContentCategory',
                'campaigns',
                'campaignShop',
                'shopRoom',
                'productGalleryProduct',
                'selectedGalleries',
                'selectedEventsEvent',
                'selectedEventsEventsList',
                'hiddenFields',
                'connectedGallery',
            ];
            $structureManager->setPathSearchLinksBlacklist($pathSearchLinksBlacklist);

            $languagesList = [];
            $languageNames = [];
            if ($childrenList = $structureElement->getChildrenList()) {
                foreach ($childrenList as &$element) {
                    if ($element->structureType == 'language') {
                        if (!$element->hidden) {
                            $languagesList[] = $element;
                        }
                        $languageNames[$element->id] = $element->title;
                    }
                }
            }

            $renderer = renderer::getInstance();
            $renderer->assign('languagesList', $languagesList);
            $renderer->assign('rootElement', $structureElement);
            $renderer->assign('languageNames', $languageNames);
        }
    }
}