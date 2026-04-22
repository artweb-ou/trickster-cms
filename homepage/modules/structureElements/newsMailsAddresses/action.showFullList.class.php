<?php

class showFullListNewsMailsAddresses extends structureElementAction
{
    /**
     * @param newsMailsAddressesElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService(renderer::class);
            $renderer->assign('contentSubTemplate', 'newsMailsAddresses.list.tpl');
        }
    }
}