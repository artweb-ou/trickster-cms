<?php

class showShoppingBasket extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param shoppingBasketElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $renderer = $this->getService('renderer');
        $renderer->assign('shoppingBasket', $structureElement);

        if ($structureElement->requested) {
            if ($currentStepElement = $structureElement->getCurrentStepElement()) {
                if ($currentStepElement->getStepElementByType('checkout')) {
                    if ($formData = $shoppingBasket->getBasketFormData()) {
                        foreach ($formData as $key => $value) {
                            $structureElement->$key = $value;
                        }
                    }
                }
            }

            $structureElement->setViewName('selection');

            $structureElement->prepareFormInformation();
        }
    }
}