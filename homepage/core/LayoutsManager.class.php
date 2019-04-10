<?php

class LayoutsManager
{
    /**
     * @var structureManager
     */
    protected $structureManager;

    public function getLayout(structureElement $structureElement, $group = '')
    {
        if ($structureElement instanceof ConfigurableLayoutsProviderInterface && ($layout = $structureElement->getCurrentLayout($group))) {
            return $layout;
        } else {
            if ($structureElement instanceof ConfigurableLayoutsElementsInterface) {
                $connectedElements = $structureElement->getLayoutProviders();
            } else {
                $connectedElements = $this->structureManager->getElementsParents($structureElement->id);
            }
            $layout = false;

            if ($connectedElements) {
                //first check only requested connected elements
                $connectedElement = reset($connectedElements);
                do {
                    if ($connectedElement->requested) {
                        $layout = $this->getLayout($connectedElement, $group);
                    }
                } while (!$layout && ($connectedElement = next($connectedElements)));

                //now check all connected elements
                if (!$layout) {
                    $connectedElement = reset($connectedElements);
                    do {
                        $layout = $this->getLayout($connectedElement, $group);
                    } while (!$layout && ($connectedElement = next($connectedElements)));
                }
            }
            return $layout;
        }
    }

    public function setStructureManager($structureManager)
    {
        $this->structureManager = $structureManager;
    }
}