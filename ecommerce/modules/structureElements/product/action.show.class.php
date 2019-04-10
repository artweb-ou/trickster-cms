<?php

class showProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($controller->getApplicationName() == 'mobile') {
            $structureElement->setViewName('show');
        } else {
            $structureElement->setViewName($this->getService('ConfigManager')->get('main.templateTypeCategoryProduct'));
        }

        $cacheKey = 'product_firstimage_' . $structureElement->id;
        if (($cacheData = $this->getService('Cache')->get($cacheKey)) !== false) {
            $structureElement->image = $cacheData['image'];
            $structureElement->originalName = $cacheData['originalName'];
        } else {
            $image = $originalName = '';
            $cacheTags = [$structureElement->id];
            $firstImage = $structureElement->getFirstImageElement();
            if ($firstImage) {
                $cacheTags[] = $firstImage->id;
                $image = $firstImage->image;
                $originalName = $firstImage->originalName;
                $structureElement->image = $image;
                $structureElement->originalName = $originalName;
            }
            $cacheData = [
                'image' => $image,
                'originalName' => $originalName,
            ];
            $this->getService('Cache')->set($cacheKey, $cacheData, 60 * 60 * 24 * 7, $cacheTags);
        }

        if ($structureElement->requested) {
            $structureElement->parentCategory = $structureElement->getRequestedParentCategory();
            $structureElement->setViewName('details');

            $languagesManager = $this->getService('languagesManager');
            $currentLanguageId = $languagesManager->getCurrentLanguageId();

            $structureElement->questionLink = '';
            if ($elements = $structureManager->getElementsByType('feedback', $currentLanguageId)) {
                $firstForm = reset($elements);
                $structureElement->questionLink = $firstForm->URL . 'product/' . $structureElement->id . '/';
            }
        }
        if ($structureElement->final) {
            $structureElement->logViewEvent();
        }
    }
}