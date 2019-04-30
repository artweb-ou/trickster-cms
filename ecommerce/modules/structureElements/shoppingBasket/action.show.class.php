<?php

class showShoppingBasket extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $renderer = $this->getService('renderer');
        $renderer->assign('shoppingBasket', $structureElement);

        if ($structureElement->requested) {
            $structureElement->setViewName('selection');

            $structureElement->prepareFormInformation();
        }
    }
}


//selection
//account