<?php

class showUpdatesDeployments extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $deployments = [];
            foreach ($structureElement->getUpdates() as $update) {
                $deployments[] = [
                    'type' => $update->type,
                    'version' => $update->version,
                    'description' => $update->description,
                ];
            }
            $renderer->assign('updates', $deployments);
            $renderer->assign('installed', (bool)(int)$controller->getParameter('installed'));
            $renderer->assign('installError', $controller->getParameter('installError'));
            $renderer->assign('contentSubTemplate', 'deployments.updates.tpl');
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [];
    }
}