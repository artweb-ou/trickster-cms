<?php

class showFolder extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param folderElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->externalUrl) {
            $structureElement->URL = $structureElement->externalUrl;
        }

        if ($structureElement->requested) {
            $structureElement->setViewName('content');

            if ($structureElement->final) {
                $application = $controller->getApplication();
                if ($application instanceof publicApplication) {
                    $subMenu = $structureElement->getSubMenuList();
                    if (!($structureElement->getContentElements()) && $subMenu) {
                        $firstMenu = reset($subMenu);
                        $controller->restart($firstMenu->URL);
                    }
                }
            }
        }
    }
}

