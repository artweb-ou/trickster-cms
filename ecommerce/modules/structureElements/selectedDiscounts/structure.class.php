<?php

class selectedDiscountsElement extends menuDependantStructureElement
{
    public $dataResourceName = 'module_selecteddiscounts';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $connectedDiscountsIds;
    protected $discountsToDisplay;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['mode'] = 'text';
        $moduleStructure['connectedDiscounts'] = 'numbersArray';

        $moduleStructure['receivedDiscountsIds'] = 'array'; // temporary
    }

    public function getDiscountsToDisplay()
    {
        if (is_null($this->discountsToDisplay)) {
            $this->discountsToDisplay = [];
            $structureManager = $this->getService('structureManager');

            if ($discountsListElements = $structureManager->getElementsByType("discountsList", $this->getService('LanguagesManager')
                ->getCurrentLanguageId())
            ) {
                $allApplicableDiscounts = [];
                foreach ($discountsListElements as &$discountsListElement) {
                    $allApplicableDiscounts = array_merge($allApplicableDiscounts, $discountsListElement->getDiscountsIds());
                }
                if ($this->mode == "auto") {
                    $idsToLoad = $allApplicableDiscounts;
                } else {
                    $connectedDiscountsIds = $this->getConnectedDiscountsIds();
                    $idsToLoad = array_intersect($connectedDiscountsIds, $allApplicableDiscounts);
                }
                foreach ($idsToLoad as &$discountId) {
                    if ($discountElement = $structureManager->getElementById($discountId)) {
                        $this->discountsToDisplay[] = $discountElement;
                    }
                }
            }
        }
        return $this->discountsToDisplay;
    }

    public function getAvailableDiscounts()
    {
        $availableDiscountsList = [];
        $discountList = [];
        $structureManager = $this->getService('structureManager');
        $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
        $publicRoot = $structureManager->getElementByMarker($marker);
        $languages = $structureManager->getElementsChildren($publicRoot->id);
        foreach ($languages as &$languageElement) {
            if ($languageElement->requested) {
                $availableDiscountsList = $structureManager->getElementsByType('discount', $languageElement->id);
            }
        }
        if ($connectedIds = $this->getConnectedDiscountsIds()) {
            foreach ($availableDiscountsList as &$discountElement) {
                $item['id'] = $discountElement->id;
                $item['title'] = $discountElement->getTitle();
                $item['select'] = in_array($discountElement->id, $connectedIds);
                $discountList[] = $item;
            }
        }
        return $discountList;
    }

    public function getConnectedDiscountsIds()
    {
        if (is_null($this->connectedDiscountsIds)) {
            $this->connectedDiscountsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, "selectedDiscountsDiscount", "parent");
        }
        return $this->connectedDiscountsIds;
    }
}