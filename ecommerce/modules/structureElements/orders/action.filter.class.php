<?php

class filterOrders extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param ordersElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $responseStatus = 'success';
        //$structureElement->prepareSettingsData();

        $structureElement->setViewName('list');

        $start = false;
        if ($controller->getParameter('start')) {
            $start = strtotime($controller->getParameter('start') . ' 00:00:00');
        }
        $end = false;
        if ($controller->getParameter('end')) {
            $end = strtotime($controller->getParameter('end') . ' 23:59:59');
        }
        $filterTypes = [];
        if ($controller->getParameter('types')) {
            $filterTypes = explode(';', $controller->getParameter('types'));
        }

        if ($start && $end && $filterTypes) {
            $structureElement->prepareFilteredData($start, $end, $filterTypes);
        }

        $renderer = $this->getService('renderer');
        $renderer->assign('responseStatus', $responseStatus);
        if ($renderer instanceof rendererPluginAppendInterface) {
            $renderer->assignResponseData('orders', $structureElement->getElementData());
        }
    }
}