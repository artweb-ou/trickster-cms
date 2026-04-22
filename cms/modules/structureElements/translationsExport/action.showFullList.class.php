<?php

class showFullListTranslationsExport extends structureElementAction
{
    /**
     * @param translationsExportElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($structureElement->final) {
            $structureElement->setTemplate("shared.content.tpl");
            $renderer = $this->getService(renderer::class);

            $renderer->assign('start', $controller->getParameter('start'));
            $renderer->assign('end', $controller->getParameter('end'));
            $renderer->assign('admin_translations', $controller->getParameter('admin_translations'));
            $renderer->assign("contentSubTemplate", "translationsExport.list.tpl");
        }
    }
}

