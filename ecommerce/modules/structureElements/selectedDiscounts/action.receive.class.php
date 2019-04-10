<?php

class receiveSelectedDiscounts extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();

            // connect discounts
            $linksManager = $this->getService('linksManager');
            if ($connectedIds = $structureElement->getConnectedDiscountsIds()) {
                foreach ($connectedIds as &$connectedId) {
                    if (!in_array($connectedId, $structureElement->receivedDiscountsIds)) {
                        $linksManager->unLinkElements($structureElement->id, $connectedId, "selectedDiscountsDiscount");
                    }
                }
            }
            foreach ($structureElement->receivedDiscountsIds as $receivedDiscountId) {
                $linksManager->linkElements($structureElement->id, $receivedDiscountId, "selectedDiscountsDiscount");
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'mode',
            'displayMenus',
            'receivedDiscountsIds',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}