<?php

trait SortedChildrenListTrait
{
    protected $childrenListSortDirection = SORT_ASC;

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $useBlackList = false)
    {
        if ($childrenList = parent::getChildrenList($roles, $linkType)) {
            $sort = [];
            foreach ($childrenList as &$element) {
                $sort[] = mb_strtolower($element->getTitle());
            }
            array_multisort($sort, $this->childrenListSortDirection, $childrenList);
        }
        return $childrenList;
    }
}