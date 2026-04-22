<?php

class showImportFormNewsMailsAddresses extends structureElementAction
{
    protected $actionsLogData;

    /**
     * @param newsMailsAddressesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $renderer = $this->getService(renderer::class);

            $structureElement->setTemplate('shared.content.tpl');
            $renderer->assign('contentSubTemplate', 'newsMailsAddresses.import.tpl');
        }
    }
}