<?php

class showFeedback extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param feedbackElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($controller->getParameter("product")) {
                $productId = (int)$controller->getParameter("product");
                $structureElement->setProductId($productId);
            }
            if ($parentElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $structureManager->setCurrentElement($parentElement);
            }
        }
        $structureElement->setViewName('form');
    }

    public function getExtraModuleFields()
    {
        return $this->structureElement->getCustomModuleFields();
    }
}