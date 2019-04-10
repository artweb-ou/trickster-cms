<?php

class showDeployments extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'deployments.show.tpl');
            $configManager = $this->getService('ConfigManager');
            $installedDeployments = $configManager->get('deployment.deployments');
            $installedDeployments = array_reverse($installedDeployments);
            $renderer->assign('installedDeployments', $installedDeployments);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [];
    }
}