<?php

class showFormDiscountsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->discountsList = [];

        $discountsElement = $structureManager->getElementByMarker('discounts');
        $discountsList = $structureManager->getElementsFlatTree($discountsElement->id);

        $linksManager = $this->getService('linksManager');
        $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'discountsList', 'parent');

        foreach ($discountsList as &$discount) {
            $discountItem = [];
            $discountItem['title'] = $discount->getTitle();
            $discountItem['select'] = isset($compiledLinks[$discount->id]);
            $discountItem['id'] = $discount->id;
            $structureElement->discountsList[] = $discountItem;
        }

        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}
