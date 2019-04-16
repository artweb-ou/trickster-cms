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

    public function getFilteredIdList($argument, $query)
    {
        $argument = (array)$argument;
        $availableProductsIds = [];
        $mode = controller::getInstance()->getParameter('mode');
        if ($mode == 'admin' || $availableProductsIds = $this->getAvailableProductsIds()) {
            $query = parent::getFilteredIdList($argument, $query);
            if ($availableProductsIds) {
                $query->whereIn('id', $availableProductsIds);
            }
            $idsFromParameterMatches = $this->findMatchesFromParameters($argument);
            if ($idsFromParameterMatches) {
                $query->orWhereIn('id', $idsFromParameterMatches);
            }
        }
        return $query;
    }

    protected function findMatchesFromParameters($argument)
    {
        $result = [];
        $languageId = $this->getService('languagesManager')->getCurrentLanguageId();
        $db = $this->getService('db');
        $query = $db->table('module_product_selection_value')->select('id')->whereIn('languageId', [$languageId, 0]);
        $query->where(function ($query) use ($argument) {
            foreach ($argument as $argumentWord) {
                $query->orWhere('title', 'like', '%' . $argumentWord . '%');
            }
        });
        $selectionsIds = [];
        if ($records = $query->get()) {
            $selectionsIds = array_column($records, 'id');
        }

        $query2 = $db->table('module_product_parameter_value')->select('productId')->distinct();
        $query2->whereIn('languageId', [$languageId, 0]);
        $query2->where(function ($query2) use ($selectionsIds, $argument) {
            if ($selectionsIds) {
                $query2->orWhereIn('value', $selectionsIds);
            }
            foreach ($argument as $argumentWord) {
                $query2->orWhere('value', 'like', '%' . $argumentWord . '%');
            }
        });

        if ($records = $query2->get()) {
            $result = array_column($records, 'productId');
        }
        return $result;
    }

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