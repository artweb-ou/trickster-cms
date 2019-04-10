<?php

trait MasterCategoriesQueringTrait
{
    protected function getTopLevelCategoriesIds()
    {
        $topLevelCategoriesIds = [];
        $structureManager = $this->getService('structureManager');
        $categoriesElementId = $structureManager->getElementIdByMarker('categories');
        $categoriesIds = $this->getService('linksManager')
            ->getConnectedIdList($categoriesElementId, 'structure', 'parent');
        if ($categoriesIds) {
            $currentLanguageId = $this->getService('languagesManager')->getCurrentLanguageId();
            foreach ($categoriesIds as &$categoryId) {
                if ($structureManager->getElementById($categoryId, $currentLanguageId)) {
                    $topLevelCategoriesIds[] = $categoryId;
                }
            }
        }
        return $topLevelCategoriesIds;
    }
}