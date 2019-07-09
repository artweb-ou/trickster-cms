<?php

class showShortcut extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('common');
        $linksManager = $this->getService('linksManager');
        $childrenIdList = false;
        if ($idList = $linksManager->getConnectedIdList($structureElement->id, 'shortcut', 'parent')) {
            $targetId = reset($idList);
            $childrenIdList = $linksManager->getConnectedIdList($targetId, '', 'parent');
        }
        $parentElements = $structureManager->getElementsParents($structureElement->id);
        if (count($parentElements) && $childrenIdList) {
            $firstParent = reset($parentElements);

            $firstParentChildren = $firstParent->getChildrenList();
            foreach ($firstParentChildren as $key => &$childElement) {
                if ($childElement->id == $structureElement->id) {
                    unset($firstParentChildren[$key]);
                }
            }

            if ($elements = $structureManager->getElementsByIdList($childrenIdList, $firstParent->id, true)) {
                $structureElement->setReplacementElements($elements);
            }
        }
    }
}

