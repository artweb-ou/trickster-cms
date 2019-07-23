<?php

class showFormDiscount extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param discountElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $deliveryTypes = [];
            if ($deliveryTypesElementId = $structureManager->getElementIdByMarker('deliveryTypes')) {
                $deliveryTypesIds = $this->getService('linksManager')
                    ->getConnectedIdList($deliveryTypesElementId, 'structure', 'parent');
                $deliveryTypes = $structureManager->getElementsByIdList($deliveryTypesIds, null, true);
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('deliveryTypes', $deliveryTypes);
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $renderer->assign('connectedDeliveryTypes', $structureElement->getConnectedDeliveryTypes());
        }
    }
}