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
            $step = 'delivery';
            if ($structureElement->actionName == 'show') {
                $user = $this->getService('user');
                $configManager = $this->getService('ConfigManager');
                if ($configManager->get('main.shoppingasketAccountStepEnabled') && $user->userName == 'anonymous') {
                    $skipped = $configManager->get('main.shoppingasketAccountStepSkippable')
                        && $controller->getParameter('step') == 'delivery';
                    if (!$skipped) {
                        $step = 'account';
                    }
                }
            }
            if ($step == 'delivery') {
                $structureElement->setViewName('selection');
            } else {
                $structureElement->setViewName($step);
            }
            $structureElement->setCurrentStep($step);
            $structureElement->prepareFormInformation();
        }
    }
}
