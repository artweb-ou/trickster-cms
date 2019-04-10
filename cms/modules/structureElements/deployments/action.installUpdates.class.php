<?php

class installUpdatesDeployments extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $installed = $structureElement->installUpdates();
            $url = $structureElement->URL . 'id:' . $structureElement->id
                . '/action:showUpdates/installed:' . (int)$installed . '/';
            if (!$installed && $structureElement->getError()) {
                $url .= 'installError:' . urlencode($structureElement->getError()) . '/';
            }
            $controller->redirect($url);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [];
    }
}