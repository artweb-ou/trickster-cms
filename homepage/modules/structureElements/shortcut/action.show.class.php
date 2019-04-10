<?php

class showShortcut extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('common');
        $linksManager = $this->getService('linksManager');
        $childrenIDList = false;
        if ($idList = $linksManager->getConnectedIdList($structureElement->id, 'shortcut', 'parent')) {
            $targetId = reset($idList);
            $childrenIDList = $linksManager->getConnectedIdList($targetId, '', 'parent');
        }
        $parentElements = $structureManager->getElementsParents($structureElement->id);
        if (count($parentElements) && $childrenIDList) {
            $firstParent = reset($parentElements);

            $firstParentChildren = $firstParent->getChildrenList();
            foreach ($firstParentChildren as $key => &$childElement) {
                if ($childElement->id == $structureElement->id) {
                    unset($firstParentChildren[$key]);
                }
            }

            if ($elements = $structureManager->getElementsByIdList($childrenIDList, $firstParent->id, 'structure')) {
                $structureElement->setReplacementElements($elements);
            }
        }
    }
}

