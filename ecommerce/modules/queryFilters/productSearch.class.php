<?php

class productSearchQueryFilter extends searchQueryFilter
{
    protected function getTypeName()
    {
        return 'product';
    }

    protected function getTitleFieldNames()
    {
        return ['title', 'code'];
    }

    protected function getContentFieldNames()
    {
        return ['content', 'introduction'];
    }

//    public function getFilteredIdList($argument, $query)
//    {
//        $argument = (array)$argument;
//        $availableProductsIds = [];
//        $mode = controller::getInstance()->getParameter('mode');
//        if ($mode == 'admin' || $availableProductsIds = $this->getAvailableProductsIds()) {
//            $query = parent::getFilteredIdList($argument, $query);
//            if ($availableProductsIds) {
//                $query->whereIn('id', $availableProductsIds);
//            }
//        }
//        return $query;
//    }

    //todo: refactor. Shouldn't this be moved to some separated model class?
    protected function getAvailableProductsIds()
    {
//        static $result;
//
//        if ($result === null) {
//            $result = [];
//            $parentsIds = [];
//            $languageId = $this->getService('languagesManager')->getCurrentLanguageId();
//            $structureManager = $this->getService('structureManager');
//            $catalogues = $structureManager->getElementsByType('productCatalogue', $languageId);
//            foreach ($catalogues as $catalogue) {
//                $catalogueProductParentsIds = $catalogue->getProductsListParentElementsIds();
//                if ($catalogueProductParentsIds) {
//                    $parentsIds = array_merge($parentsIds, $catalogueProductParentsIds);
//                }
//            }
//            if ($parentsIds) {
//                $conditions = [];
//                $conditions[] = [
//                    "parentStructureId",
//                    "IN",
//                    $parentsIds,
//                ];
//                $linksCollection = persistableCollection::getInstance("structure_links");
//                if ($records = $linksCollection->conditionalLoad('distinct(childStructureId)', $conditions, [], [], [], true)) {
//                    foreach ($records as &$record) {
//                        $result[] = $record['childStructureId'];
//                    }
//                }
//            }
//        }
//        return $result;
    }
}