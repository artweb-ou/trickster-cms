<?php

class receiveShortcut extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');
            $structureElement->prepareActualData();

            if ($structureElement->title != '') {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();

            if ($targetElement = $structureManager->getElementById($structureElement->target)) {
                $linkExists = false;
                if ($elementLinks = $linksManager->getElementsLinks($structureElement->id, 'shortcut', 'parent')) {
                    foreach ($elementLinks as &$link) {
                        if ($link->childStructureId != $structureElement->target) {
                            $link->delete();
                        } else {
                            $linkExists = true;
                        }
                    }
                }
                if (!$linkExists) {
                    $linksManager->linkElements($structureElement->id, $targetElement->id, 'shortcut');
                }
            }

            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['title', 'target'];
    }

    public function setValidators(&$validators)
    {
    }
}