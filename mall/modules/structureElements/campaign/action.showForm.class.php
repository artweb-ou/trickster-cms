<?php

class showFormCampaign extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->shopElements = [];
            $shopsElement = $structureManager->getElementByMarker('shops');
            if ($shopsElement) {
                $shopChildrens = $shopsElement->getChildrenList();
                $connectedShopId = $structureElement->getConnectedShopId();
                foreach ($shopChildrens as $shopElement) {
                    $item = [];
                    $item['id'] = $shopElement->id;
                    $item['title'] = $shopElement->title;
                    $item['structureName'] = $shopElement->structureName;
                    $item['select'] = $connectedShopId == $shopElement->id;
                    $structureElement->shopElements[] = $item;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}